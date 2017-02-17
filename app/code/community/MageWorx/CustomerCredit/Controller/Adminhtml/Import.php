<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Controller_Adminhtml_Import extends Mage_Adminhtml_Controller_Action
{
    
    const   FIELD_NAME_SOURCE_FILE = 'import_file';
    public  $errors = array();
    
    
    protected function _initAction()
    {
        $this->_title($this->__('Import/Export'))
            ->loadLayout()
            ->_setActiveMenu('system/importexport');
        return $this;
    }
    
    /**
     * Import/Export working directory (source files, result files, lock files etc.).
     *
     * @return string
     */
    
    public static function getWorkingDir()
    {
        return Mage::getBaseDir('var') . DS . 'importexport' . DS;
    }
    
    /**
     * Upload Import file
     * 
     * @return string
     */
    public function uploadFile()
    {
        $uploader  = Mage::getModel('core/file_uploader', self::FIELD_NAME_SOURCE_FILE);
        $uploader->skipDbProcessing(true);
        $result    = $uploader->save(self::getWorkingDir());
        $uploadedFile = $result['path'] . $result['file'];
        if(!$uploadedFile) {
            $this->errors[] = Mage::helper('mageworx_customercredit')->__("File can't uploaded");
        }
        return $uploadedFile;
    }
    
    /**
     * Read file content
     * 
     * @param string $filename
     * @return array
     */
    public function readFile($filename = '') 
    {
        if(!$filename) return;
        $content = array();
        ini_set("auto_detect_line_endings", true);
        $fp = fopen($filename, "r");
        while (!feof($fp))
        {
            $line = fgetcsv($fp, 1000);
            $content[] = $line;
        }
        array_shift($content);
        ini_set("auto_detect_line_endings", false);
        if(!sizeof($content)) {
            $this->errors[] = Mage::helper('mageworx_customercredit')->__("File is empty");
        }
        return $content;
    }

    /**
     * Check access (in the ACL) for current user.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/convert/import');
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $maxUploadSize = Mage::helper('importexport')->getMaxUploadSize();
        $this->_getSession()->addNotice(
            $this->__('Total size of uploadable files must not exceed %s', $maxUploadSize)
        );
        $this->_initAction()
            ->_title($this->__('Loyalty Booster Import'))
            ->_addBreadcrumb($this->__('Loyalty Booster Import'), $this->__('Loyalty Booster Import'));
       
        $this->renderLayout();
    }
    
    public function importAction() {
        $fileName = $this->uploadFile();
        $content = $this->readFile($fileName);
        Mage::getSingleton('admin/session')->setCustomerCreditImportFileContent($content);
        $this->loadLayout();
        $this->renderLayout();
    }    
    
    public function runGenerateAction() {
        $import = Mage::getModel('mageworx_customercredit/import')->run();
        
        $result = array();
        $result['text'] = $this->__('Import customer %1$s credits, processed %3$s of %2$s records (%4$s%%)...', $import->customerEmail,$import->totalRecords, $import->currentInc, round($import->currentInc/$import->totalRecords*100, 2));
        $next = $import->currentInc+1;
        if($next<=$import->totalRecords) {
            array_push($this->errors,$import->errors);
            $result['url'] = $this->getUrl('*/*/runGenerate/', array('next'=>$next));
        }
        else {
            $result['stop']= 1;
            $result['url'] = $this->getUrl('*/*/index/');
            Mage::getSingleton('admin/session')->setCustomerCreditImportFileContent(null);
            Mage::getSingleton('admin/session')->addSuccess($error);
        }
        foreach ($this->errors as $error) {
            Mage::getSingleton('admin/session')->addError($error);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}