<?php

class Entangled_Custom_Block_Checkout_Amazonpurchase extends Mage_Checkout_Block_Cart_Abstract{

    /**
     * todo: move to config
     */
    const ACCESS_KEY = "AKIAIEOQSWCL7454SCHQ";
    const ASSOCIATE_TAG = "entngled-20";

    public function getAmazonUrl(){
        if(!$this->hasData("amazon_url")){
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $cartItems = $quote->getAllVisibleItems();
            $url = "http://www.amazon.com/gp/aws/cart/add.html?";
            $cnt = 1;
            $nonAmazonProducts = array();
            foreach ($cartItems as $item) {
                $productId = $item->getProductId();
                $product = Mage::getModel('catalog/product')->load($productId);
                if ($product->getAsin()){
                    if ($cnt > 1){
                        $url .= "&";
                    }
                    $url .= "ASIN.$cnt=" . $product->getData('asin') . "&Quantity.$cnt=1";
                    $cnt += 1;
                } else {
                    $nonAmazonProducts[] = $product;
                }
            }
            $url .= "&AWSAccessKeyId=".self::ACCESS_KEY."&AssociateTag=".self::ASSOCIATE_TAG;

            $this->setData("amazon_url",$cnt > 1 ? $url : false);
        }

        return $this->getData("amazon_url");
    }

    public function hasAmazonItems(){

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        $cartItems = $quote->getAllVisibleItems();

        foreach ($cartItems as $item) {

        }
    }
}