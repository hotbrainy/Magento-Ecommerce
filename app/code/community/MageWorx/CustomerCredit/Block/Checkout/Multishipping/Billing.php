<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Checkout_Multishipping_Billing extends Mage_Checkout_Block_Multishipping_Billing {
    public function isPartialPayment() {
        return Mage::helper('mageworx_customercredit')->isPartialPayment($this->getQuote(), Mage::getSingleton('customer/session')->getCustomerId(), Mage::app()->getStore()->getWebsiteId());
    }
    
    public function getMethodsWithCustomercredit() {
        if(version_compare(Mage::getVersion(), '1.6.0.0', '<')){
            return parent::getMethods();
        }
        
        $methods = $this->getData('methods');
        if (is_null($methods)) {
            $quote = $this->getQuote();
            $store = $quote ? $quote->getStoreId() : null;
            $methods = $this->helper('payment')->getStoreMethods($store, $quote);
            $total = $quote->getGrandTotal();
            foreach ($methods as $key => $method) {
                if ($this->_canUseMethod($method)
                    && ($total >= 0
                        || $method->getCode() == 'free'
                        || ($quote->hasRecurringItems() && $method->canManageRecurringProfiles()))) {
                    $this->_assignMethod($method);
                } else {
                    unset($methods[$key]);
                }
            }
            
            if (count($methods)==1) {
                foreach($methods as $item){
                    if($item->getCode() == 'customercredit' && $this->isPartialPayment()!=2){
                        $methods = array();
                    }
                }
            }
            
            $this->setData('methods', $methods);
        }
        return $methods;
    }
    
    public function getMethods() {
        $origMethods = $this->getMethodsWithCustomercredit();
        // sort customercredit to top
        $methods = array();
        foreach ($origMethods as $method) {
            $code = $method->getCode();
            if ($code=='customercredit') array_unshift($methods, $method); else $methods[]=$method;
        }
        return $methods;
    }
}