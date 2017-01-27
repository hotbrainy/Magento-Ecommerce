<?php

class Entangled_Custom_Model_Rewrite_CustomerCredit_Payment_Method_Customercredit extends MageWorx_CustomerCredit_Model_Payment_Method_Customercredit {

    /**
     * Is Credit Availible
     * @param Mage_Sales_Model_Quote $quote
     * @return boolean
     */
    public function isAvailable($quote=null) {
        if (!$this->_getHelper()->isEnabled()) { // || $credit <= 0
            return false;
        }
        return true;
    }

    public function isApplicableToQuote($quote,$checks) {
        if (!$this->_getHelper()->isEnabled()) { // || $credit <= 0
            return false;
        }
        return true;
    }

}