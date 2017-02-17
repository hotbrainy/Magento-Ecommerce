<?php
class Entangled_RestCatalog_Block_Shopby_Top extends Amasty_Shopby_Block_Top {

    /**
     * @param Amasty_Shopby_Model_Page|null $page
     */
    protected function _handleCanonical($page = null)
    {
        return;
    }

//    protected function _prepareLayout()
//    {
//        $this->setCacheLifetime(null);
//
//        /** @var Amasty_Shopby_Block_Catalog_Product_List_Toolbar $toolbar */
//        $toolbar = $this->getLayout()->createBlock('amshopby/catalog_product_list_toolbar');
//        $toolbar->replacePager();
//
//
//        /** @var Mage_Catalog_Model_Layer $layer */
//        $layer = Mage::getSingleton('catalog/layer');
//        $category = $layer->getCurrentCategory();
//
//        if ($this->_isPageHandled($category)) {
//            $this->handleExtraAttributes();
//            return parent::_prepareLayout();
//        }
//
//        $robotsIndex = 'index';
//        $robotsFollow = 'follow';
//
//
//        $filters = Mage::getResourceModel('amshopby/filter_collection')
//            ->addTitles()
//            ->setOrder('position');
//        $hash = array();
//
//        /** @var Amasty_Shopby_Helper_Data $helper */
//        $helper = Mage::helper('amshopby');
//
//        $currentBrand = $this->getCurrentBrandPageBrand();
//        $appliedFiltersCount = 0;
//        foreach ($filters as $f) {
//            /** @var Amasty_Shopby_Model_Filter $f */
//            $code = $f->getAttributeCode();
//            $vals = $helper->getRequestValues($code, $f->getBackendType());
//            if ($vals) {
//                foreach ($vals as $v) {
//                    $hash[$v] = $f->getShowOnList();
//                }
//
//                if (!$currentBrand || $currentBrand->getFilterId() != $f->getId()) {
//                    $appliedFiltersCount++;
//
//                    if ($f->getSeoNofollow()) {
//                        $robotsFollow = 'nofollow';
//                    }
//                    if ($f->getSeoNoindex()) {
//                        $robotsIndex = 'noindex';
//                    }
//                }
//            }
//        }
//
//        if ($appliedFiltersCount > 1)
//        {
//            if (Mage::getStoreConfig('amshopby/seo/noindex_multiple'))
//                $robotsIndex = 'noindex';
//        }
//
//        $priceVals = Mage::app()->getRequest()->getParam('price');
//        if ($priceVals) {
//            if ($helper->getSeoPriceNofollow()){
//                $robotsFollow = 'nofollow';
//            }
//            if ($helper->getSeoPriceNoindex()){
//                $robotsIndex = 'noindex';
//            }
//        }
//
//        /*
//         * Check Category Settings
//         */
//        $currentCategoryId = $category->getId();
//        $catNoIndex = Mage::getStoreConfig('amshopby/seo/cat_noindex');
//        if ($catNoIndex != '') {
//            $categoriesIds = array_flip(explode(",", $catNoIndex));
//            if (isset($categoriesIds[$currentCategoryId])) {
//                $robotsIndex = 'noindex';
//            }
//        }
//
//        $catNoFollow = Mage::getStoreConfig('amshopby/seo/cat_nofollow');
//        if ($catNoFollow != '') {
//            $categoriesIds = array_flip(explode(",", $catNoFollow));
//            if (isset($categoriesIds[$currentCategoryId])) {
//                $robotsFollow = 'nofollow';
//            }
//        }
//        $this->handleExtraAttributes();
//
//        /** @var Mage_Page_Block_Html_Head $head */
//        $head = $this->getLayout()->getBlock('head');
//        if ($head){
//            if ('noindex' == $robotsIndex || 'nofollow' == $robotsFollow){
//                $head->setRobots($robotsIndex .', '. $robotsFollow);
//            }
//        }
//
//        if (!$hash){
//            return call_user_func(array(get_parent_class(get_parent_class($this)), '_prepareLayout'));
//        }
//
//        $options = Mage::getResourceModel('amshopby/value_collection')
//            ->addFieldToFilter('option_id', array('in' => array_keys($hash)))
//            ->load();
//
//        $cnt = $options->count();
//        if (!$cnt){
//            return call_user_func(array(get_parent_class(get_parent_class($this)), '_prepareLayout'));
//        }
//
//        //some of the options value have wrong value;
//        if ($cnt && $cnt < count($hash)){
//            return call_user_func(array(get_parent_class(get_parent_class($this)), '_prepareLayout'));
//            // or make 404 ?
//        }
//
//        // sort options by attribute ids and add "show_on_list" property
//        foreach ($options as $opt){
//            /** @var Amasty_Shopby_Model_Value $opt */
//            $id = $opt->getOptionId();
//
//            $opt->setShowOnList($hash[$id]);
//            $hash[$id] = clone $opt;
//        }
//
//        // unset "fake"  options (not object)
//        foreach ($hash as $id => $opt){
//            if (!is_object($opt)){
//                unset($hash[$id]);
//            }
//        }
//        if (!$hash){
//            return call_user_func(array(get_parent_class(get_parent_class($this)), '_prepareLayout'));
//        }
//
//        $this->options = $hash;
//
//        if ($head){
//            $this->changeMetaData($head);
//        }
//
//        $this->addBrandBreadcrumb();
//
//        $this->addBottomCmsBlocks();
//
//        $this->changeCategoryData($category);
//
//        return call_user_func(array(get_parent_class(get_parent_class($this)), '_prepareLayout'));
//    }

}
