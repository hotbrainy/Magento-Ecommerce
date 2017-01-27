<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Rules_Customer extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('mageworx_customercredit/rules_customer', 'id');
    }
    
    public function loadByRuleAndCustomer($object, $ruleId, $customerId) {
        $read = $this->_getReadAdapter();
        if ($read) {  
            $select = $read->select()
                    ->from($this->getMainTable())
                    ->where('rule_id = ?', $ruleId)
                    ->where('customer_id = ?', $customerId)
                    ->limit(1);
 
            $data = $read->fetchRow($select);
            if ($data) {
                $object->addData($data);
            }
        }
    }
    
    
    
}
