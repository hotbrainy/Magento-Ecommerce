<?php
class Idev_OneStepCheckout_Helper_Extraproducts extends Mage_Core_Helper_Abstract
{
    function getProductIds()
    {
        $ids_raw = Mage::getStoreConfig('onestepcheckout/extra_products/product_ids');
        
        if($ids_raw && $ids_raw != '') {
            return explode(',', $ids_raw);        
        }
        else {
            return array();
        }
    }
   
    function productInCart($product_id)
    {
        $cart = Mage::helper('checkout/cart')->getCart();
        foreach($cart->getItems() as $item) {
            if($item->getProduct()->getId() == $product_id) {
                return true;
            }
        }
        return false;
    }

    function isValidExtraProduct($product_id)
    {
        $ids = $this->getProductIds();
        if(in_array($product_id, $ids)) {
            return true;
        }

        return false;
    }

    function hasExtraProducts()
    {
        if(count($this->getProductIds()) > 0) {
            return true;
        }
        return false;
    }

    function getExtraProducts()
    {
        $items = array();
        foreach($this->getProductIds() as $id) {
            if($id != '') {
                try {
                    $item = Mage::getModel('catalog/product')->load($id);
                } catch(Exception $e) {
                    continue;
                }
                if($item->getId()) {
                    $items[] = $item;
                }
            }
        }
        return $items;
    }

}
