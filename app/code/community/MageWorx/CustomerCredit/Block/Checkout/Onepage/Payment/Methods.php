<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Checkout_Onepage_Payment_Methods extends Mage_Checkout_Block_Onepage_Payment_Methods {
   
    public function isPartialPayment() {
        return Mage::helper('mageworx_customercredit')->isPartialPayment($this->getQuote(), Mage::getSingleton('customer/session')->getCustomerId(), Mage::app()->getStore()->getWebsiteId());
    }
    
    public function getMethods() {
        //$origMethods = $this->getMethodsWithCustomercredit();
        $origMethods = parent::getMethods();
        

        // sort customercredit to top
        $methods = array();
        foreach ($origMethods as $method) {
            $code = $method->getCode();
            if ($code=='customercredit') array_unshift($methods, $method); else $methods[] = $method;
        }
        return $methods;
    }
    
    public function needShowRechageLink() {
        $marker = $this->isPartialPayment();
     
        if($marker<2) {
            return true;
        }
        return false;
    }
}