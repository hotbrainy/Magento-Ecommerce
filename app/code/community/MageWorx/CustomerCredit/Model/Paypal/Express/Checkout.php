<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class  MageWorx_CustomerCredit_Model_Paypal_Express_Checkout extends Mage_Paypal_Model_Express_Checkout
{
    
    public function start($returnUrl, $cancelUrl) {
        $this->_quote->collectTotals();                        
                
        $address = $this->_quote->getIsVirtual() ? $this->_quote->getBillingAddress() : $this->_quote->getShippingAddress();        
        
        // get credit amount
        $baseCustomerCreditAmount = $address->getBaseCustomerCreditAmount();
        $customerCreditAmount = $address->getCustomerCreditAmount();
                
        // apply credit        
        // fix if credit in order > Subtotal
        if ($baseCustomerCreditAmount > $address->getBaseSubtotal()) {
            $baseCustomerCreditAmount -= $address->getBaseShippingAmount() + $address->getBaseTaxAmount();
            $customerCreditAmount -= $address->getShippingAmount() + $address->getTaxAmount();            
            $this->_quote->setBaseShippingAmount(0)->setShippingAmount(0);
            $this->_quote->setBaseTaxAmount(0)->setTaxAmount(0);            
            $address->setBaseShippingAmount(0)->setShippingAmount(0);
            $address->setBaseTaxAmount(0)->setTaxAmount(0);
        }
        
        if ($baseCustomerCreditAmount>0) {
            $this->_quote->setBaseDiscountAmount(abs($this->_quote->getBaseDiscountAmount()) + $baseCustomerCreditAmount);
            $this->_quote->setDiscountAmount(abs($this->_quote->getDiscountAmount()) + $customerCreditAmount);
            
            $address->setBaseDiscountAmount($address->getBaseDiscountAmount() + $baseCustomerCreditAmount);
            $address->setDiscountAmount($address->getDiscountAmount() + $customerCreditAmount);
        }    
        
        return parent::start($returnUrl, $cancelUrl);
        
    }

    
}
