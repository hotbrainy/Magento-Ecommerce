<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
class Amasty_Shopby_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    
        // init category
        $categoryId = (int) Mage::app()->getStore()->getRootCategoryId();
        if (!$categoryId) {
            $this->_forward('noRoute', 'index', 'cms');
            return;
        }

        /** @var Mage_Catalog_Model_Category $category */
        $category = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($categoryId);

        Mage::register('current_category', $category);
        Mage::getSingleton('catalog/session')->setLastVisitedCategoryId($category->getId());

        // need to prepare layer params
        try {
            Mage::dispatchEvent('catalog_controller_category_init_after',
                array('category' => $category, 'controller_action' => $this));
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return;
        }
        // observer can change value
        if (!$category->getId()){
            $this->_forward('noRoute', 'index', 'cms');
            return;
        }

        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        if ($helper->useSolr()) {
            Mage::register('_singleton/catalog/layer', Mage::getSingleton('enterprise_search/catalog_layer'));
        }

        $this->loadLayout();

        /* load swatches js for 1.9.1+ */
        $this->checkAddSwatches();

        /** @var Mage_Page_Block_Html_Head $head */
        $head = $this->getLayout()->getBlock('head');
        if ($head && $head->getTitle() == '') {
            $head->setTitle($category->getName());
        }

        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('checkout/session');

        $this->renderLayout();
    }

    protected function checkAddSwatches(){
        $moduleEnabled = Mage::helper('amshopby')->isModuleEnabled('Mage_ConfigurableSwatches');
        $listNameAfterBlock = $this->getLayout()->getBlock('product_list.name.after');
        $listAfterBlock = $this->getLayout()->getBlock('product_list.after');
        if ($moduleEnabled && $listNameAfterBlock && $listAfterBlock) {
            $this->getLayout()->getBlock('head')->addItem('skin_js','js/configurableswatches/product-media.js');
            $this->getLayout()->getBlock('head')->addItem('skin_js','js/configurableswatches/swatches-list.js');

            $block = $this->getLayout()->createBlock(
                'Mage_Core_Block_Template',
                'product_list.swatches',
                array('template' => 'configurableswatches/catalog/product/list/swatches.phtml')
            );

            $listNameAfterBlock->append($block);

            $block = $this->getLayout()->createBlock(
                'Mage_ConfigurableSwatches_Block_Catalog_Media_Js_List',
                'configurableswatches.media.js.list'
            );

            $listAfterBlock->append($block);
        }
    }
}