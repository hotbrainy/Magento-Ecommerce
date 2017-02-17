<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
class Amasty_Shopby_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{
    const MIDDLE    = 0;
    const BEGINNING = 1;

    /** @var  Zend_Controller_Request_Http */
    protected $request;

    protected $urlMode;

    public function match(Zend_Controller_Request_Http $request)
    {
        if (Mage::app()->getStore()->isAdmin()) {
            return false;
        }

        $this->request = $request;
        $this->urlMode = Mage::getStoreConfig('amshopby/seo/urls');

        $isMatched = false;

        if (!$isMatched && $this->urlMode == Amasty_Shopby_Model_Source_Url_Mode::MODE_SHORT) {
            $isMatched = $this->matchForwardShort();
        }

        if (!$isMatched) {
            $isMatched = $this->matchForwardMultilevel();
        }

        return $isMatched;
    }

    /**
     * @return bool
     */
    protected function matchForwardShort()
    {
        $pageId = $this->request->getPathInfo();
        $pageId = $this->getUrlHelper()->checkRemoveSuffix($pageId);
        $pageId = trim($pageId, '/');
        $p = strrpos($pageId, '/');

        if ($p) {
            $cat = substr($pageId,0, $p);
            $params = substr($pageId, $p);
        } else {
            $cat = '';
            $params = $pageId;
        }

        /** @var Amasty_Shopby_Model_Url_Parser $shortParser */
        $shortParser = Mage::getModel('amshopby/url_parser');
        $query = $shortParser->parseParams($params);
        if ($query === false) {
            return false;
        }

        $query = array_merge($this->request->getQuery(), $query);
        $this->request->setQuery($query);
        if (strlen($cat)){ // normal category
            try {
                $this->forwardCategory($cat);
            } catch (Mage_Core_Controller_Varien_Exception $e) {
                return false;
            }
        }
        else { // root category
            if (!Mage::getStoreConfig('amshopby/seo/enable_shopby_page')) {
                /** @var Amasty_Shopby_Helper_Attributes $helper */
                $helper = Mage::helper('amshopby/attributes');
                $brand = $helper->getRequestedBrandOption();
                if (!$brand) {
                    return false;
                }
            }

            $this->forwardShopby();
        }
        return true;
    }

    protected function matchForwardMultilevel()
    {
        $pageId = $this->request->getPathInfo();
        $pageId = $this->getUrlHelper()->checkRemoveSuffix($pageId);
        $pageId = trim($pageId, '/') . '/';

        $cat = $this->matchMultilevel($pageId);
        if ($cat === false) {
            return false;
        }

        if ($cat){ // normal category
            try {
                $this->forwardCategory($cat);
            } catch (Mage_Core_Controller_Varien_Exception $e) {
                return false;
            }
        }
        else { // root category
            if (!Mage::getStoreConfig('amshopby/seo/enable_shopby_page')) {
                /** @var Amasty_Shopby_Helper_Attributes $helper */
                $helper = Mage::helper('amshopby/attributes');
                $brand = $helper->getRequestedBrandOption();
                if (!$brand) {
                    return false;
                }
            }

            $this->forwardShopby();
        }

        // Redirect to new short URL
        if ($this->urlMode == Amasty_Shopby_Model_Source_Url_Mode::MODE_SHORT && Mage::getStoreConfig('amshopby/seo/redirects_enabled')) {
            /** @var Amasty_Shopby_Model_Url_Builder $urlBuilder */
            $urlBuilder = Mage::getModel('amshopby/url_builder');
            $urlBuilder->reset();
            $urlBuilder->mode = $this->urlMode;
            $url = $urlBuilder->getUrl();
            Mage::app()->getResponse()->setRedirect($url, 301);
        }
        return true;
    }

    /**
     * @param string $pageId
     * @return string|bool $cat
     */
    protected function matchMultilevel($pageId)
    {
        $reservedKey = Mage::getStoreConfig('amshopby/seo/key') . '/';


        //  canon/
        //  electronics - false

        //  electronics/shopby/canon/
        //  electronics/shopby/red/
        //  electronics/shopby/

        //  shopby/
        //  shopby/red/
        //  shopby/canon/ - false
        //  shopby/manufacturer-canon/ - false
        //  manufacturer-canon/ - true

        // starts from shopby
        $isAllProductsPage = substr($pageId, 0, strlen($reservedKey)) == $reservedKey;

        // has shopby in the middle
        $isCategoryPage = false !== strpos($pageId, '/' . $reservedKey);

        if (!$this->urlMode) // Prevent using SEO urls with 'Use SEO URLs' disabled
        {
            // If path info have something after reserved key
            if (($isAllProductsPage || $isCategoryPage) &&
                substr($pageId, -strlen($reservedKey), strlen($reservedKey)) != $reservedKey)
            {
                return false;
            }
        }

        if ($isAllProductsPage){
            // no support for old style urls
            if ($this->hasBrandIn(self::MIDDLE, $pageId)){
                return false;
            }
        }

        if (!$isAllProductsPage && !$isCategoryPage){
            if (!$this->hasBrandIn(self::BEGINNING, $pageId)){
                return false;
            }
            //it is brand page and we modify the url to be in the old style
            $pageId = $reservedKey . $pageId;
        }

        // get layered navigation params as string
        $splitKey = $isCategoryPage ? '/'.$reservedKey : $reservedKey;
        list($cat, $params) = explode($splitKey, $pageId, 2);
        $params = trim($params, '/');
        if ($params || $params === '0')
            $params = explode('/', $params);

        // remember for future use in the helper
        if ($params || $params === '0'){
            Mage::register('amshopby_current_params', $params);
            if (false === $this->getUrlHelper()->saveParams($this->request)) {
                return false;
            }
        }

        return $cat;
    }

    protected function hasBrandIn($position, $pageId)
    {
        $code = Mage::getStoreConfig('amshopby/brands/attr');
        $code = trim(str_replace('_', Mage::getStoreConfig('amshopby/seo/special_char'), $code));


        if (!$code) {
            return false;
        }

        $options = $this->getUrlHelper()->getAllFilterableOptionsAsHash();
        //check if we have brand names
        if (empty($options[$code])) {
            return false;
        }

        $found[self::MIDDLE]    = false;
        $found[self::BEGINNING] = false;
        foreach ($options[$code] as $key => $id) {
            if (!Mage::getStoreConfig('amshopby/seo/hide_attributes')){
                $key = $code . Mage::getStoreConfig('amshopby/seo/option_char') . $key;

            }

            if (0 === strpos($pageId, $key . '/')) {
                $found[self::BEGINNING] = true;
            }

            if (false !== strpos($pageId, '/' . $key . '/')) {
                $found[self::MIDDLE] = true;
            }
        }

        return $found[$position];
    }

    /**
     * @param string $cat
     * @throws Mage_Core_Controller_Varien_Exception
     */
    protected function forwardCategory($cat)
    {
        $cat = trim($cat, '/');

        // we do not use Mage::getVersion() here as it is not defined in the old versions.
        $isVersionEE13 = Mage::helper('amshopby')->isModuleEnabled('Enterprise_UrlRewrite');
        $suffix = $this->getUrlHelper()->getUrlSuffix();
        if ($isVersionEE13) {
            $urlRewrite = Mage::getModel('enterprise_urlrewrite/url_rewrite');
            /* @var $urlRewrite Enterprise_UrlRewrite_Model_Url_Rewrite */

            if (version_compare(Mage::getVersion(), '1.13.0.2', '>=')) {
                $catReqPath = array('request' => $cat . $suffix, 'whole' => $cat);
            }
            else {
                $catReqPath = array($cat);
            }

            $urlRewrite
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($catReqPath);
        }
        else {
            /* @var Mage_Core_Model_Url_Rewrite $urlRewrite */
            $urlRewrite = Mage::getModel('core/url_rewrite');

            $cat = $cat . $suffix;
            $catReqPath = $cat;

            $urlRewrite
                ->setStoreId(Mage::app()->getStore()->getId())
                ->loadByRequestPath($catReqPath);
        }

        if (!$urlRewrite->getId()){
            $store = $this->request->getParam('___from_store');
            $store = Mage::app()->getStore($store)->getId();
            if ($store){
                $urlRewrite->setData(array())
                    ->setStoreId($store)
                    ->loadByRequestPath($catReqPath);
            }

            if (!$urlRewrite->getId()){
                if (is_array($catReqPath)) {
                    $catReqPath = current($catReqPath);
                }
                throw new Mage_Core_Controller_Varien_Exception("Routing: Cannot forward to category view page: " . $catReqPath);
            }
        }

        $this->request->setPathInfo($cat);
        $this->request->setModuleName('catalog');
        $this->request->setControllerName('category');
        $this->request->setActionName('view');

        if ($isVersionEE13) {
            $categoryId = str_replace('catalog/category/view/id/', '', $urlRewrite->getTargetPath());
            $this->request->setParam('id', $categoryId);
        }
        else {
            $categoryId = $urlRewrite->getCategoryId();
            $this->request->setParam('id', $categoryId);
            $urlRewrite->rewrite($this->request);
        }

        Mage::register('amshopby_forwarded_category_id', $categoryId);
    }

    protected function forwardShopby()
    {
        $reservedKey = Mage::getStoreConfig('amshopby/seo/key');
        $realModule = 'Amasty_Shopby';

        $this->request->setPathInfo($reservedKey);
        $this->request->setModuleName('amshopby');
        $this->request->setRouteName('amshopby');
        $this->request->setControllerName('index');
        $this->request->setActionName('index');
        $this->request->setControllerModule($realModule);

        $file = Mage::getModuleDir('controllers', $realModule) . DS . 'IndexController.php';
        include $file;

        //compatibility with 1.3
        $class = $realModule . '_IndexController';
        $controllerInstance = new $class($this->request, $this->getFront()->getResponse());

        $this->request->setDispatched(true);
        $controllerInstance->dispatch('index');
    }

    protected function getUrlHelper()
    {
        /** @var Amasty_Shopby_Helper_Url $helper */
        $helper = Mage::helper('amshopby/url');
        return $helper;
    }
}
