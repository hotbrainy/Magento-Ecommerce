<?php
/**
 * Custom Publisher Models
 * 
 * Add custom model types, such as author, which can be used as a product
 * attribute while proviting additional details.
 * 
 * @license 	http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 * @copyright	Steven Brown March 12, 2016
 * @author		Steven Brown <steveb.27@outlook.com>
 */


/**
 * Product reports admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class SteveB27_Publish_Adminhtml_Author_Report_SalesController extends Mage_Adminhtml_Controller_Report_Abstract
{
	protected function _construct()
	{
        $this->setUsedModuleName('SteveB27_Publish');
    }
    
    /**
     * Add report/authors breadcrumbs
     *
     * @return SteveB27_Publish_Adminhtml_ReportController
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(Mage::helper('publish')->__('Authors'), Mage::helper('publish')->__('Authors'));
        return $this;
    }

	public function indexAction()
	{
		$this->_initAction()
			->_setActiveMenu('report/author/sales')
			->_addBreadcrumb(Mage::helper('publish')->__('Sales Volume by Author'), Mage::helper('publish')->__('Sales Volume by Author'))
			->_addContent($this->getLayout()->createBlock('publish/adminhtml_author_report_sales'))
			->renderLayout();
	}

    /**
     * Export Author Sales report to CSV format action
     *
     */
    public function exportSalesCsvAction()
    {
        $fileName   = 'products_ordered.csv';
        $content    = $this->getLayout()
            ->createBlock('publish/adminhtml_author_report_sales')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export Author Sales report to XML format action
     *
     */
    public function exportSalesExcelAction()
    {
        $fileName   = 'products_ordered.xml';
        $content    = $this->getLayout()
            ->createBlock('publish/adminhtml_author_report_sales')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Check is allowed for report
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        
        switch ($action) {
            case 'sales':
                return Mage::getSingleton('admin/session')->isAllowed('report/author/sales');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/products');
                break;
        }
    }
}
