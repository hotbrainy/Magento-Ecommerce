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

class SteveB27_Publish_Block_Adminhtml_Author_Report_Sales_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    /**
     * Sub report size
     *
     * @var int
     */
    protected $_subReportSize = 0;

    /**
     * Initialize Grid settings
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('gridAuthorSales');
    }

    /**
     * Prepare collection object for grid
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()
            ->initReport('publish/author_report_sales_collection');
        return $this;
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('author_id', array(
			'header'    =>Mage::helper('publish')->__('Author ID'),
			'width'     =>'60px',
			'index'     =>'publish_author',
		));		

		$this->addColumn('author_name', array(
			'header'    =>Mage::helper('publish')->__('Author Name'),
			'width'     =>'120px',
			'index'     =>'author_name',
			'align'     =>'right'
		));

		$this->addColumn('base_price_total', array(
			'header'    =>Mage::helper('publish')->__('Total Product Base Price ($)'),
			'width'     =>'60px',
			'index'     =>'base_price_total',
			'align'     =>'right',
			'total'     =>'sum',
			'type'      =>'number'

		));

        $this->addExportType('*/*/exportSalesCsv', Mage::helper('publish')->__('CSV'));
        $this->addExportType('*/*/exportSalesExcel', Mage::helper('publish')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
