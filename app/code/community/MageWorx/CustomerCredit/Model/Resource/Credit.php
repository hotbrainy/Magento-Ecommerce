<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Credit extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() {
        $this->_init('mageworx_customercredit/credit', 'credit_id');
    }

    public function loadByCustomerAndWebsite($object, $customerId, $websiteId) {
        $read = $this->_getReadAdapter();
        if ($read) {
            if (!is_null($websiteId)) {
                $select = $read->select()
                        ->from($this->getMainTable())
                        ->where('customer_id = ?', $customerId)
                        ->where('website_id = ?', $websiteId)
                        ->limit(1);
            } else {
                $select = $read->select()
                        ->from($this->getMainTable())
                        ->where('customer_id = ?', $customerId)
                        ->limit(1);
            }

            $data = $read->fetchRow($select);
            if ($data) {
                $object->addData($data);
            }
        }
    }
}