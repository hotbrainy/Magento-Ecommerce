<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Import_Credit extends MageWorx_CustomerCredit_Model_Import_Abstract
{
    /**
     * Change data
     * @param array $entity
     * @return boolean
     */
    protected function _changeData($entity = array()) {
        try {
            $website_code   = $entity[0];
            $customerEmail  = $entity[1];
            $creditValue    = $entity[2];
            $comment        = $entity[3];
            
            $website = Mage::getSingleton('core/website')->load($website_code,'code'); 
            $this->customerEmail = $customerEmail;
            $customer = Mage::getModel('customer/customer')->setWebsiteId($website->getWebsiteId())->loadByEmail($customerEmail);
            
            $customerCredit = Mage::getModel('mageworx_customercredit/credit', $customer);
            $customerCredit->processImport($creditValue,$comment);

       } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
        return true;
    }
}
