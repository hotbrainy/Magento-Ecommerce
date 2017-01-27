<?php

class Entangled_Custom_Block_Rewrite_Catalog_Product_List_Upsell extends Mage_Catalog_Block_Product_List_Upsell {

    protected function _prepareData()
    {
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */

        $this->_itemCollection = $product->getCollection();
        $this->_itemCollection
            ->addAttributeToFilter(array(
                array("attribute" =>"book_imprint", "in"=>explode(",",$product->getData("book_imprint"))),
            ))
            ->addAttributeToFilter("entity_id",array("neq"=>$product->getId()))
            ->addAttributeToSelect('required_options')
            ->addStoreFilter()
            ->setPageSize(24)
            ->getSelect()->order('rand()');

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
            Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
                Mage::getSingleton('checkout/session')->getQuoteId()
            );
            $this->_addProductAttributesAndPrices($this->_itemCollection);
        }
//        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_itemCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

        $this->_itemCollection->load();

        foreach ($this->_itemCollection as $product) {
            $product->setDoNotUseCategoryId(true);
        }

        return $this;
    }

}