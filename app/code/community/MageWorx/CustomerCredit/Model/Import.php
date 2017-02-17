<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Import extends Mage_ImportExport_Model_Import
{
    public  $errors = array();
    public  $totalRecords = 0;
    public  $currentInc = 0;
    public  $customerEmail;
    private $_fileContent;
    
    /**
     * Init import
     */
    private function _init() {
        $content = Mage::getSingleton('admin/session')->getCustomerCreditImportFileContent();
        $this->_fileContent = $content;
        $this->totalRecords = sizeof($this->_fileContent);
    }
    
    /**
     * Change credot value
     * @param array $entity
     * @return boolean
     */
    private function _changeCustomerCredit($entity = array()) {
        try {
            $website_code   = $entity[0];
            $customerEmail  = $entity[1];
            $creditValue    = $entity[2];
            $comment        = $entity[3];
            $website = Mage::getSingleton('core/website')->load($website_code,'code'); 
            $this->customerEmail = $customerEmail;
            $customer = Mage::getModel('customer/customer')->setWebsiteId($website->getWebsiteId())->loadByEmail($customerEmail);
            
            $customerCredit = Mage::getModel('mageworx_customercredit/credit', $customer);
            $customerCredit->proccessImport($creditValue, $comment);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }
        return true;
    }
    
    /**
     * Run import
     * @return MageWorx_CustomerCredit_Model_Import
     */
    public function run() 
    {
        $this->_init();
        $content = $this->_fileContent;
        $currentValueId = Mage::app()->getRequest()->getParam('next',1);
        $this->currentInc = $currentValueId;
        $this->_changeCustomerCredit($content[$currentValueId-1]);
        return $this;
    }
}
