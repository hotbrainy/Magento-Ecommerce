<?php

class SteveB27_GoodReads_Model_Observer {

    public function catalogControllerProductInitAfter(Varien_Event_Observer $observer){
        $product = $observer->getProduct();
        $attribute = Mage::getStoreConfig('catalog/goodreads/isbn_attribute');
        $isbn = $product->getData($attribute);
        if ($isbn != Entangled_Purchasediscount_Helper_Data::DISCOUNT_SKU) {
            $goodreads = Mage::helper('goodreads')->isbnBookReviews($isbn);
            Mage::register("current_goodreads_review",$goodreads);
        }
    }

}