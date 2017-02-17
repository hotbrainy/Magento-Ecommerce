<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
 
class MageWorx_CustomerCredit_Adminhtml_Mageworx_Customercredit_ReportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() {
        $this->loadLayout()
            ->_setActiveMenu('report/mageworx_customercredit');
      $this->_title($this->__('Reports'))->_title($this->__('Loyalty Booster Report'));
        $this->_addContent($this->getLayout()->createBlock('mageworx_customercredit/adminhtml_report'));
        $this->renderLayout();
    }
    
    public function gridAction() {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function exportCsvAction() {
        $fileName   = 'loyalty_booster.csv';
        $content    = $this->getLayout()->createBlock('mageworx_customercredit/adminhtml_report_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export new accounts report grid to Excel XML format
     */
    public function exportExcelAction() {
        $fileName   = 'loyalty_booster.xml';
        $content    = $this->getLayout()->createBlock('mageworx_customercredit/adminhtml_report_grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('report/mageworx_customercredit');
    }
}