<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
 
class MageWorx_CustomerCredit_Adminhtml_Mageworx_Customercredit_Import_CreditController extends MageWorx_CustomerCredit_Controller_Adminhtml_Import
{
    /**
     * Index action.
      * @return void
     */
    public function indexAction() {
        $maxUploadSize = Mage::helper('importexport')->getMaxUploadSize();
        $this->_getSession()->addNotice(
            $this->__('Total size of uploadable files must not exceed %s', $maxUploadSize)
        );
        $this->_initAction()
            ->_title($this->__('Loyalty Booster Import'))
            ->_addBreadcrumb($this->__('Loyalty Booster Import'), $this->__('Loyalty Booster Import'));
       
        $this->renderLayout();
    }
  
    public function runGenerateAction() {
        $import = Mage::getModel('mageworx_customercredit/import_credit')->run();
        
        $result = array();
        $result['text'] = $this->__('Import customer %1$s credits, processed %3$s of %2$s records (%4$s%%)...', $import->customerEmail,$import->totalRecords, $import->currentInc, round($import->currentInc/$import->totalRecords*100, 2));
        $next = $import->currentInc+1;
        if($next<=$import->totalRecords) {
            array_push($this->errors,$import->errors);
            $result['url'] = $this->getUrl('*/*/runGenerate/', array('next'=>$next));
        } else {
            $result['stop']= 1;
            $result['url'] = $this->getUrl('*/*/index/');
            $error = $this->errors;
            Mage::getSingleton('admin/session')->setCustomerCreditImportFileContent(null);
            Mage::getSingleton('admin/session')->addSuccess($error);
        }
        foreach ($this->errors as $error) {
            Mage::getSingleton('admin/session')->addError($error);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('system/convert/mageworx_customercredit_import_credits');
    }
}