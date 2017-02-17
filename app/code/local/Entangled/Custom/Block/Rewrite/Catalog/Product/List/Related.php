<?php

class Entangled_Custom_Block_Rewrite_Catalog_Product_List_Related extends Mage_Catalog_Block_Product_List_Related {

    protected function _prepareData()
    {
        $product = Mage::registry('product');
        /* @var $product Mage_Catalog_Model_Product */

        if($this->getData("same_author")){
            $this->_itemCollection = $product->getCollection();
            if(strpos($product->getData("publish_author"),",") !== false){
                $this->_itemCollection->addAttributeToFilter("publish_author",array("in"=>explode(",",$product->getData("publish_author"))));
            }else{
                $this->_itemCollection->addAttributeToFilter("publish_author",$product->getData("publish_author"));
            }
            $this->_itemCollection
                ->addAttributeToFilter("entity_id",array("neq"=>$product->getId()))
                ->addAttributeToSelect('required_options')
                ->addStoreFilter()
                ->setPageSize(12);
        }elseif($product->getData("book_series") && $this->getData("only_series")){
            $this->_itemCollection = $product->getCollection();
            $this->_itemCollection
                ->addAttributeToFilter("book_series",$product->getData("book_series"))
                ->addAttributeToFilter("entity_id",array("neq"=>$product->getId()))
                ->addAttributeToSelect('required_options')
                ->addAttributeToSort("book_series_2","ASC")
                ->addStoreFilter();
        }else{
            $this->_itemCollection = $product->getCollection();
            $this->_itemCollection->addAttributeToFilter("entity_id",0);
        }

        if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
            // Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_itemCollection,
            //     Mage::getSingleton('checkout/session')->getQuoteId()
            // );
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