<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
class Amasty_Shopby_Model_Observer
{
    public function handleControllerFrontInitRouters($observer)
    {
        $observer->getEvent()->getFront()
            ->addRouter('amshopby', new Amasty_Shopby_Controller_Router());
    }

    public function handleCatalogControllerCategoryInitAfter($observer)
    {
        if (Mage::getStoreConfig('amshopby/seo/urls')) {
            if (Mage::getStoreConfig('amshopby/seo/redirects_enabled')) {
                $this->checkRedirectToSeo();
            }

            /** @var Mage_Core_Controller_Front_Action $controller */
            $controller = $observer->getEvent()->getControllerAction();
            /** @var Mage_Catalog_Model_Category $cat */
            $cat = $observer->getEvent()->getCategory();

            if (!Mage::helper('amshopby/url')->saveParams($controller->getRequest())){
                if ($cat->getId()  == Mage::app()->getStore()->getRootCategoryId()){
                    $cat->setId(0);
                    return;
                }
                else {
                    Mage::helper('amshopby')->error404();
                }
            }

            if ($cat->getDisplayMode() == 'PAGE' && Mage::registry('amshopby_current_params')){
                $cat->setDisplayMode('PRODUCTS');
            }
        }

        Mage::helper('amshopby')->restrictMultipleSelection();
    }

    protected function checkRedirectToSeo()
    {
        if (Mage::registry('amshopby_forwarded_category_id')) {
            // Already forwarded by our router
            return;
        }

        if (Mage::app()->getRequest()->getParam('am_landing')) {
            // Not implemented and works incorrectly
            return;
        }

        /** @var Amasty_Shopby_Model_Url_Builder $urlBuilder */
        $urlBuilder = Mage::getModel('amshopby/url_builder');
        $urlBuilder->reset();

        $isAJAX = Mage::app()->getRequest()->getParam('is_ajax', false);
        $isAJAX = $isAJAX && Mage::app()->getRequest()->isXmlHttpRequest();
        if ($isAJAX) {
            $urlBuilder->setAllowAjaxFlag(true);
        }

        $seoUrl = $urlBuilder->getUrl();
        $pSeo = strpos($seoUrl, '?');
        $tSeo = $pSeo ? substr($seoUrl, 0, $pSeo) : $seoUrl;

        $currentUrl = Mage::helper('core/url')->getCurrentUrl();
        $pCurrent = strpos($currentUrl, '?');
        $tCurrent = $pCurrent ? substr($currentUrl, 0, $pCurrent) : $currentUrl;

        if ($tCurrent != $tSeo) {
            Mage::app()->getResponse()->setRedirect($seoUrl, 301);
        }
    }

    public function handleLayoutRender()
    {
        if (Mage::app()->getRequest()->getParam('is_scroll', false))
            return;

        /** @var Mage_Core_Model_Layout $layout */
        $layout = Mage::getSingleton('core/layout');
        $headBlock = $layout->getBlock('head');
        if (!$layout)
            return;

        $isAJAX = Mage::app()->getRequest()->getParam('is_ajax', false);
        $isAJAX = $isAJAX && Mage::app()->getRequest()->isXmlHttpRequest();
        if (!$isAJAX)
            return;

        $layout->removeOutputBlock('root');

        $page = $layout->getBlock('category.products');
        if (!$page){
            $page = $layout->getBlock('search.result');
        }

        if (!$page)
            return;

        $container = $layout->createBlock('core/template', 'amshopby_container');
        $container->setData('page', utf8_encode($this->_removeAjaxParam($page->toHtml())));
        $container->setData('title', $headBlock ? $headBlock->getTitle() : null);

        $blocks = array();
        foreach ($layout->getAllBlocks() as $b){
            if (!in_array($b->getNameInLayout(), array('amshopby.navleft','amshopby.navleft2','amshopby.navtop','amshopby.navright', 'amshopby.top', 'amshopby.bottom', 'amfinder89'))){
                continue;
            }
            $b->setIsAjax(true);
            $html = $b->toHtml();
            if (!$html && false !== strpos($b->getBlockId(), 'amshopby-filters-'))
            {
                // compatibility with "shopper" theme
                // @see catalog/layer/view.phtml
                $queldorei_blocks = Mage::registry('queldorei_blocks');
                if ($queldorei_blocks AND !empty($queldorei_blocks['block_layered_nav']))
                {
                    $html = $queldorei_blocks['block_layered_nav'];
                }
            }
            if ($b->getBlockId()) {
                $blocks[$b->getBlockId()] = $this->_removeAjaxParam($html);
            }
        }

        if (!$blocks)
            return;

        $container->setData('blocks', $blocks);

        $layout->addOutputBlock('amshopby_container', 'toJson');
    }

    protected function _removeAjaxParam($html)
    {
        $html = str_replace('?___SID=U&amp;', '?', $html);
        $html = str_replace('?___SID=U', '', $html);
        $html = str_replace('&___SID=U', '', $html);

        return $html;
    }

    public function handleBlockOutput($observer)
    {
        if (!Mage::getStoreConfigFlag('amshopby/block/ajax'))
            return;

        /* @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();

        $classMatch = $block instanceof Mage_Catalog_Block_Category_View || $block instanceof Mage_CatalogSearch_Block_Result || $block instanceof Mage_Core_Block_Text_List;
        $nameMatch = $block->getNameInLayout() == 'category.products' || $block->getNameInLayout() == 'search.result';

        if ($classMatch && $nameMatch) {
            $transport = $observer->getTransport();
            $html = $transport->getHtml();

            if (strpos($html, "amshopby-page-container") === FALSE){
                $html = '<div class="amshopby-page-container" id="amshopby-page-container">' .
                            $html .
                            '<div style="display:none" class="amshopby-overlay"><div></div></div>'.
                        '</div>';

                $transport->setHtml($html);
            }
        }
    }

    /**
     * Reset search engine if it is enabled for catalog navigation
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetCurrentCatalogLayer(Varien_Event_Observer $observer)
    {
        if ($this->_getDataHelper()->useSolr()) {
            Mage::register('_singleton/catalog/layer', Mage::getSingleton('enterprise_search/catalog_layer'));
        }
    }

    /**
     * Reset search engine if it is enabled for search navigation
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetCurrentSearchLayer(Varien_Event_Observer $observer)
    {
        if ($this->_getDataHelper()->useSolr()) {
            Mage::register('_singleton/catalogsearch/layer', Mage::getSingleton('enterprise_search/search_layer'));
        }
    }

    public function settingsChanged()
    {
        /** @var Amasty_Shopby_Model_Mysql4_Filter_Collection $filterCollection */
        $filterCollection = Mage::getResourceModel('amshopby/filter_collection');
        $count = $filterCollection->count();
        if ($count == 0) {
            Mage::getResourceModel('amshopby/filter')->refreshFilters();
        }
        $this->invalidateCache();
    }

    public function attributeChanged()
    {
        Mage::getResourceModel('amshopby/filter')->refreshFilters();
        $this->invalidateCache();
    }

    protected function invalidateCache()
    {
        $this->_getDataHelper()->invalidateCache();
    }

    protected function _getDataHelper()
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        return $helper;
    }
}
