<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
class Amasty_Shopby_Helper_Url extends Mage_Core_Helper_Abstract
{
    protected $_options    = null;
    protected $_attributes = null;
    protected $_allParamsAreValid = null;

    protected function _getCurrentUrlWithoutParams()
    {
        $url = Mage::helper('core/url')->getCurrentUrl();
        // remove query params if any
        $pos = max(0, strpos($url, '?'));
        if ($pos) {
            $url = substr($url, 0, $pos);
        }
        return $url;
    }

    public function getCanonicalUrl()
    {
        $key = Mage::getStoreConfig('amshopby/seo/key');
        $category = $this->_getCurrentCategory();
        $canonicalType = Mage::getStoreConfig('amshopby/seo/canonical' . (is_object($category) ? '_cat' : ''));

        switch ($canonicalType) {
            case Amasty_Shopby_Model_Source_Canonical::CANONICAL_KEY:
                return $category ? $category->getUrl() : (Mage::getBaseUrl() . $key);

            case Amasty_Shopby_Model_Source_Canonical::CANONICAL_CURRENT_URL:
                return $this->_getCurrentUrlWithoutParams();

            case Amasty_Shopby_Model_Source_Canonical::CANONICAL_FIRST_ATTRIBUTE_VALUE:
                return $this->_getFirstAttributeValueUrl();
        }

        return null;
    }

    protected function _getCurrentCategory()
    {
        /** @var Mage_Catalog_Model_Layer $layer */
        $layer = Mage::getSingleton('catalog/layer');
        $category = $layer->getCurrentCategory();
        $isDefault = $category->getId() == Mage::app()->getStore()->getRootCategoryId();

        return $isDefault ? null : $category;
    }

    protected function _getFirstAttributeValueUrl()
    {
        /** @var Amasty_Shopby_Model_Url_Builder $urlBuilder */
        $urlBuilder = Mage::getModel('amshopby/url_builder');
        $urlBuilder->reset();
        $urlBuilder->clearQuery();

        $query = Mage::app()->getRequest()->getQuery();
        $hash = $this->getAllFilterableOptionsAsHash();

        /** @var Amasty_Shopby_Helper_Data $dataHelper */
        $dataHelper = Mage::helper('amshopby');

        foreach (array_keys($query) as $code) {
            if (array_key_exists($code, $hash)) {
                $values = $dataHelper->getRequestValues($code);
                if (!$values) {
                    continue;
                }
                $value = $values[0];
                $foundOptionAlias = array_search($value, $hash[$code]);

                if ($foundOptionAlias !== false) {
                    $urlBuilder->changeQuery(array(
                        $code => $value
                    ));

                    break;
                }
            }
        }
        $url = $urlBuilder->getUrl();

        return $url;
    }

    /**
     * @deprecated Now it is a facade to Amasty_Shopby_Model_Url
     */
    public function getFullUrl($query=array(), $clear=false, $cat = null)
    {
        /** @var Amasty_Shopby_Model_Url_Builder $builder */
        $builder = Mage::getModel('amshopby/url_builder');
        $builder->reset();

        if ($clear) {
            $builder->clearQuery();
            $moduleName = Mage::app()->getRequest()->getModuleName();
            if (Mage::app()->getRequest()->getParam('am_landing') || in_array($moduleName, array('sqli_singlesearchresult', 'catalogsearch' ,'categorysearch'))) {
                $builder->clearCategory();
            }
        }

        if ($cat === false) {
            $builder->category = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId());
        } else if (is_object($cat)) {
            $query['cat'] = $cat->getId();
        }

        $builder->changeQuery($query);

        $url = $builder->getUrl();
        return $url;
    }

    public function getOptionUrl($attributeCode, $attributeValue)
    {
        /** @var Amasty_Shopby_Model_Url_Builder $urlBuilder */
        $urlBuilder = Mage::getModel('amshopby/url_builder');
        $urlBuilder->reset();
        $urlBuilder->clearCategory();
        $urlBuilder->clearModule();
        $urlBuilder->clearQuery();
        $urlBuilder->changeQuery(array(
            $attributeCode => $attributeValue,
        ));
        return $urlBuilder->getUrl();
    }

    public function saveParams($request)
    {
        if (!is_null($this->_allParamsAreValid)){
            return $this->_allParamsAreValid;
        }
        $this->_allParamsAreValid = true;

        $options = $this->getAllFilterableOptionsAsHash();
        if (!$options){
            return true;
        }

        $currentParams = Mage::registry('amshopby_current_params');
        if (!$currentParams){
            return true;
        }

        // brand-amd-canon/price-100,200 or  amd-canon/100,200
        $hideAttributeNames = Mage::getStoreConfig('amshopby/seo/hide_attributes');

        foreach ($currentParams as $params){

            $attrCode = '';

            $params   = explode(Mage::getStoreConfig('amshopby/seo/option_char'), $params);
            $firstOpt = $params[0];

            if ($hideAttributeNames && !$this->isDecimal($firstOpt)){
                $attrCode = $this->_getAttributeCodeByOptionKey($firstOpt, $options);
            }
            else {
                $attrCode = $firstOpt;
                array_shift($params); // remove first element
            }

            if ($attrCode && isset($options[$attrCode])){
                $query = array();

                if ($this->isDecimal($attrCode)){

                    $v = $params[0];
                    if (count($params) > 1){
                        $v = $params[0] . Mage::getStoreConfig('amshopby/seo/option_char') . $params[1];
                    }

                    if ($v === '' || is_null($v)){
                        $this->_allParamsAreValid = false;
                        return false;
                    }
                    /*
                      * Convert AttrCode back to contrast_ratio (magento way) from contrast-ratio
                      */
                    $query[$this->_convertAttributeToMagento($attrCode)] = $v;
                }
                else {
                    $ids = $this->_convertOptionKeysToIds($params, $options[$attrCode]);
                    $ids = $ids ? join(',', $ids) : $request->getParam($attrCode);  // fix for store changing

                    $v = is_array($ids) ? '' : $ids; // just in case
                    if (!$v){
                        $this->_allParamsAreValid = false;
                        return false;
                    }
                    /*
                      * Convert AttrCode back to contrast_ratio (magento way) from contrast-ratio
                      */
                    $query[$this->_convertAttributeToMagento($attrCode)] = $v;
                }

                $request->setQuery($query);
            }
            else { // we have undefined string
                $this->_allParamsAreValid = false;
                return false;
            }
        }

        return true;

    }

    public function isOnBrandPage()
    {
        if (Mage::app()->getRequest()->getModuleName() != 'amshopby')
            return false;

        $cat = Mage::registry('current_category');
        $params = Mage::app()->getRequest()->getQuery();
        return $this->isBrandPage($cat, $params);
    }

    public function isBrandPage($cat, $params)
    {
        if (Mage::app()->getRequest()->getParam('am_landing')) {
            return false;
        }

        $attrCode = trim(Mage::getStoreConfig('amshopby/brands/attr'));
        if (!$attrCode) {
            return false;
        }

        if ($cat){
            $rootId = (int) Mage::app()->getStore()->getRootCategoryId();
            if ($cat->getId() != $rootId) {
                return false;
            }
        }

        if (empty($params[$attrCode])){
            return false;
        }

        return true;
    }

    public function isDecimal($attrCode)
    {
        $attrCode = $this->_convertAttributeToMagento($attrCode);
        /** @var Amasty_Shopby_Helper_Attributes $attributeHelper */
        $attributeHelper = Mage::helper('amshopby/attributes');
        $map = $attributeHelper->getDecimalAttributeCodeMap();
        return isset($map[$attrCode]) ? $map[$attrCode] : false;
    }

    public function getAllFilterableOptionsAsHash()
    {
        return Mage::helper('amshopby/attributes')->getAllFilterableOptionsAsHash();
    }

    private function _convertIdToKeys($options, $ids)
    {
        $options = array_flip($options);

        $keys = array();
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        foreach ($ids as $optionId){
            if (isset($options[$optionId])){
                $keys[] = $options[$optionId];
            }
        }
        return join(Mage::getStoreConfig('amshopby/seo/option_char'), $keys);
    }

    public function _formatAttributePartMultilevel($attrCode, $ids)
    {
        if ($this->isDecimal($attrCode)){
            return $attrCode . Mage::getStoreConfig('amshopby/seo/option_char') . $ids; // always show price and other decimal attributes
        }

        $options = $this->getAllFilterableOptionsAsHash();
        $part    = $this->_convertIdToKeys($options[$attrCode], $ids);

        if (!$part){
            return '';
        }

        $hideAttributeNames = Mage::getStoreConfig('amshopby/seo/hide_attributes');
        $part =  $hideAttributeNames ? $part : ($attrCode . Mage::getStoreConfig('amshopby/seo/option_char') . $part);

        return $part;
    }

    public function _formatAttributePartShort($attrCode, $ids)
    {
        if ($this->isDecimal($attrCode)){
            return $attrCode . Mage::getStoreConfig('amshopby/seo/option_char') . $ids; // always show other decimal attributes
        }

        $options = $this->getAllFilterableOptionsAsHash();
        $part    = $this->_convertIdToKeys($options[$attrCode], $ids);

        return $part;
    }

    private function _getAttributeCodeByOptionKey($key, $optionsHash)
    {
        if (!$key && $key !== '0') {
            return false;
        }

        foreach ($optionsHash as $code => $values){
            if (isset($values[$key])){
                return $code;
            }
        }

        return false;
    }

    private function _convertOptionKeysToIds($keys, $values)
    {
        $ids = array();
        foreach ($keys as $k){
            if (isset($values[$k])){
                $ids[] = $values[$k];
            }
        }

        return $ids;
    }

    public function _convertAttributeToMagento($attrCode)
    {
        return str_replace(array(Mage::getStoreConfig('amshopby/seo/option_char'), Mage::getStoreConfig('amshopby/seo/special_char')), '_', $attrCode);
    }

    public function checkRemoveSuffix($url)
    {
        $suffix = $this->getUrlSuffix();
        if ($suffix == '') {
            return $url;
        }

        $l = strlen($suffix);
        if (substr_compare($url, $suffix, -$l) == 0) {
            $url = substr($url, 0, -$l);
        }

        return $url;
    }

    public function checkAddSuffix($url)
    {
        $suffix = $this->getUrlSuffix();
        if ($suffix == '') {
            return $url;
        }

        $l = strlen($suffix);
        if (strlen($url) < $l || substr_compare($url, $suffix, -$l) != 0) {
            $url.= $suffix;
        }

        return $url;
    }

    public function getUrlSuffix()
    {
        $suffix = Mage::getStoreConfig('catalog/seo/category_url_suffix');
        if ($suffix && '/' != $suffix && '.' != $suffix[0]){
            $suffix = '.' . $suffix;
        }
        return $suffix;
    }
}
