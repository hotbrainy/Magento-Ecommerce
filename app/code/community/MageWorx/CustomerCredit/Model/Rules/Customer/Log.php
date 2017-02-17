<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Rules_Customer_Log extends Mage_Rule_Model_Rule
{
    protected function _construct() {
        parent::_construct();
        $this->_init('mageworx_customercredit/rules_customer_log');
        $this->setIdFieldName('id');
    }
    
    public function loadByRuleAndCustomer($ruleId, $customerId) {
	$this->getResource()->loadByRuleAndCustomer($this, $ruleId, $customerId);
        return $this;
    }
}