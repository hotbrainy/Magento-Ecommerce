<?php
/**
 * Elasticsearch observer
 *
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Model_Observer
{
    /**
     * Adds search weight parameter in attribute form
     *
     * @param Varien_Event_Observer $observer
     */
    public function onEavAttributeEditFormInit(Varien_Event_Observer $observer)
    {
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = $observer->getEvent()->getAttribute();
        /** @var Varien_Data_Form $form */
        $form = $observer->getEvent()->getForm();
        $fieldset = $form->addFieldset('elasticsearch_fieldset', array(
            'legend'    => Mage::helper('elasticsearch')->__('Elasticsearch Settings')
        ));

        $fieldset->addField('search_weight', 'select', array(
            'name' => 'search_weight',
            'label' => Mage::helper('elasticsearch')->__('Search Weight'),
            'note' => Mage::helper('elasticsearch')->__('Boost some attributes by giving them a higher weight.'),
            'values' => array(
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5,
                6 => 6,
                7 => 7,
                8 => 8,
                9 => 9,
                10 => 10,
            ),
        ), 'is_searchable');

        if ($attribute->getAttributeCode() == 'name') {
            $form->getElement('is_searchable')->setDisabled(1);
        }
    }

    /**
     * Add missing delete event for categories
     *
     * @param Varien_Event_Observer $observer
     * @throws Exception
     */
    public function onCategoryDeleteCommitAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Category $category */
        $category = $observer->getEvent()->getCategory();
        Mage::getSingleton('index/indexer')
            ->processEntityAction($category, Mage_Catalog_Model_Category::ENTITY, Mage_Index_Model_Event::TYPE_DELETE);
    }

    /**
     * Add missing save event for CMS pages
     *
     * @param Varien_Event_Observer $observer
     * @throws Exception
     */
    public function onCmsPageSaveCommitAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Cms_Model_Page $page */
        $page = $observer->getEvent()->getObject();
        Mage::getSingleton('index/indexer')
            ->processEntityAction($page, 'cms_page', Mage_Index_Model_Event::TYPE_SAVE);
    }

    /**
     * Add missing delete event for CMS pages
     *
     * @param Varien_Event_Observer $observer
     * @throws Exception
     */
    public function onCmsPageDeleteCommitAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Cms_Model_Page $page */
        $page = $observer->getEvent()->getObject();
        Mage::getSingleton('index/indexer')
            ->processEntityAction($page, 'cms_page', Mage_Index_Model_Event::TYPE_DELETE);
    }

    /**
     * Handle product auto redirect if only one product is found
     */
    public function onSearchResultRenderBefore()
    {
        if (Mage::getStoreConfigFlag('elasticsearch/product/auto_redirect')) {
            $layout = Mage::getSingleton('core/layout');
            /** @var Mage_Catalog_Block_Product_List $block */
            $block = $layout->getBlock('search_result_list');
            if ($block && $block->getLoadedProductCollection()->getSize() === 1) {
                /** @var Mage_Catalog_Model_Product $product */
                $product = $block->getLoadedProductCollection()->getFirstItem();
                $url = $product->getProductUrl();
                if ($url) {
                    Mage::app()->getResponse()->setRedirect($url)->sendResponse();
                }
            }
        }
    }

    /**
     * Save config settings for fast autocomplete
     *
     * @param Varien_Event_Observer $observer
     */
    public function onConfigSectionSaveAfter(Varien_Event_Observer $observer)
    {
        $section = $observer->getEvent()->getSection();
        if (in_array($section, array('design', 'search', 'catalog', 'elasticsearch'))) {
            Mage::app()->reinitStores();
            Mage::helper('elasticsearch/autocomplete')->saveConfig();
        }
    }
}
