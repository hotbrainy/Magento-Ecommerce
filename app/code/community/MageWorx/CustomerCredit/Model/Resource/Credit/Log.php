<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Credit_Log extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() {
        $this->_init('mageworx_customercredit/credit_log', 'log_id');
    }

    protected function _beforeSave(Mage_Core_Model_Abstract $object) {
        $object->setActionDate(Mage::getSingleton('core/date')->gmtDate());
        return parent::_beforeSave($object);
    }    
    
    public function loadByOrderAndAction($object, $orderId, $actionType, $rulesCustomerId) {
        $read = $this->_getReadAdapter();
        if ($read) {
            if ($rulesCustomerId) {
                $select = $read->select()
                    ->from($this->getMainTable())
                    ->where('order_id = ?', $orderId)
                    ->where('rules_customer_id = ?', $rulesCustomerId)
                    ->where('action_type = ?', $actionType)
                    ->limit(1);
            } else {
                $select = $read->select()
                    ->from($this->getMainTable())
                    ->where('order_id = ?', $orderId)
                    ->where('action_type = ?', $actionType)
                    ->limit(1);
            }    
 
            $data = $read->fetchRow($select);
            if ($data) {
                $object->addData($data);
            }
        }
    }
    

}