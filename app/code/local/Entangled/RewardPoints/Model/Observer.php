<?php

class Entangled_RewardPoints_Model_Observer {

    public function controllerActionPredispatchCmsIndexIndex(Varien_Event_Observer $observer){
        if($_GET["empty_cart"]){
            Mage::getSingleton("customer/session")->addError("You need to have products in your cart to checkout, and your cart is empty.");
        }
    }

    public function salesQuoteAddressDiscountItem(Varien_Event_Observer $observer){
        $item = $observer->getItem();
        $address = $item->getQuote()->getBillingAddress();
        $total = $address->getSubtotalInclTax();
        $credit = $address->getCustomerCreditAmount();
        $discount = $credit/$total;
        $item->setDiscountAmount($item->getRowTotalInclTax() * $discount);
        $item->setBaseDiscountAmount($item->getBaseRowTotalInclTax() * $discount);
    }


}