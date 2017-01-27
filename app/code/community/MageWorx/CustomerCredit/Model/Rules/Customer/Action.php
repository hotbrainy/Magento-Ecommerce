<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Rules_Customer_Action extends Mage_Rule_Model_Rule
{
    const MAGEWORX_CUSTOMER_ACTION_PLACEORDER   = 6;
    const MAGEWORX_CUSTOMER_ACTION_REGISTRATION = 7;
    const MAGEWORX_CUSTOMER_ORDER_COMPLETE      = 8;

    protected function _construct() {
        parent::_construct();
        $this->_init('mageworx_customercredit/rules_customer_action');
        $this->setIdFieldName('id');
    }
    
    public function getActionTypesOptions() {
        return array(
            self::MAGEWORX_CUSTOMER_ACTION_PLACEORDER   => Mage::helper('mageworx_customercredit')->__('Customer Place Order'),
            self::MAGEWORX_CUSTOMER_ACTION_REGISTRATION => Mage::helper('mageworx_customercredit')->__('Customer Registered in Site'),
            self::MAGEWORX_CUSTOMER_ORDER_COMPLETE      => Mage::helper('mageworx_customercredit')->__("Customer's order complete"),
        );
    }
     
    public function loadByRuleAndCustomer($ruleId, $customerId) {
	$this->getResource()->loadByRuleAndCustomer($this, $ruleId, $customerId);
        return $this;
    }
}