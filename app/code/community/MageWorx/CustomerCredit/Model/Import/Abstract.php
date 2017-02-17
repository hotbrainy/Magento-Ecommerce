<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Import_Abstract extends Mage_ImportExport_Model_Import
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
     * Run import
     * @return MageWorx_CustomerCredit_Model_Import_Abstract
     */
    public function run() {
        $this->_init();
        $content = $this->_fileContent;
        $currentValueId = Mage::app()->getRequest()->getParam('next',1);
        $this->currentInc = $currentValueId;
        $this->_changeData($content[$currentValueId-1]);
        return $this;
    }
}
