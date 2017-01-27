<?php

class Entangled_Reports_Adminhtml_Reports_BooksController extends Mage_Adminhtml_Controller_Report_Abstract {

    /**
     * Add report/sales breadcrumbs
     *
     * @return Mage_Adminhtml_Report_SalesController
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('entangled_reports')->__('Entangled'), Mage::helper('entangled_reports')->__('Books Sales'));
        return $this;
    }


    public function salesAction() {
        $this->_initAction()
            ->renderLayout();
    }


    public function filesAction() {
        $this->_initAction()
            ->renderLayout();
    }

    public function exportFilesCsvAction() {
        ini_set('max_execution_time', 900);

        $fileName = 'books_files.csv';
        $content = $this->getLayout()->createBlock('entangled_reports/adminhtml_files_grid')
            ->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportCsvAction() {
        $fileName = 'books_sales.csv';
        $content = $this->getLayout()->createBlock('entangled_reports/adminhtml_books_grid')
            ->getCsv();
        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction() {
        $fileName = 'books_sales.xml';
        $content = $this->getLayout()->createBlock('entangled_reports/adminhtml_books_grid')
            ->getXml();
        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }

    protected function _isAllowed(){
        return $this->_getSession()->isAllowed('report/salesroot/sales');
    }

}