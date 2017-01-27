<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Rules_Customer_Action_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('mageworx_customercredit/rules_customer_action');
    }
    
    public function loadByRuleAndCustomer($ruleId,$customerId)
    {
        $this->getSelect()
               ->where('rule_id = ?',$ruleId)
               ->where('customer_id = ?',$customerId);
        return $this;
    }
}