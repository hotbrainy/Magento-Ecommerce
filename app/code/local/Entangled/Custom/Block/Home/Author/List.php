<?php

class Entangled_Custom_Block_Home_Author_List extends Infortis_Ultimo_Block_Product_List_Featured  {

    protected $_template = "catalog/product/list_featured_slider.phtml";

    /**
     * Retrieve loaded category collection.
     * Variables collected from CMS markup: category_id, product_count, is_random
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection))
        {
            $this->_productCollection = Mage::getModel("catalog/product")->getCollection();
            $this->_productCollection
                ->addAttributeToFilter("publish_author",$this->getAuthorId())
                ->addAttributeToSelect('required_options')
                ->addStoreFilter()
                ->setPageSize(15);

            if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
                Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter($this->_productCollection,
                    Mage::getSingleton('checkout/session')->getQuoteId()
                );
                $this->_addProductAttributesAndPrices($this->_productCollection);
            }
//        Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($this->_itemCollection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_productCollection);

            $this->_productCollection->load();

            foreach ($this->_itemCollection as $product) {
                $product->setDoNotUseCategoryId(true);
            }
        }
        return $this->_productCollection;
    }

}