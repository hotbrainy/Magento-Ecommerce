<?php
class Entangled_Reports_Block_Adminhtml_Books_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('booksSalesGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setSubReportSize(false);
    }

    protected function _prepareCollection() {
        $authorAttribute = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product','publish_author');
        /** @var Mage_Sales_Model_Resource_order_Collection $collection */
        $collection = Mage::getModel('sales/order')->getCollection();
        $resource = $collection->getResource();
        $collection->getSelect()->join( array('order_item'=> $resource->getTable("order_item")), 'order_item.order_id = main_table.entity_id', array());
        $collection->getSelect()->join( array('product'=> $resource->getTable("catalog/product")), 'order_item.sku = product.sku', array());
        $collection->getSelect()->join( array(
            'author'=> $resource->getTable("catalog/product")."_varchar"),
            'product.entity_id = author.entity_id and attribute_id = '.$authorAttribute,
            array()
        );
        $collection->getSelect()->joinLeft(
            array('returns'=> $resource->getTable("entangled_returns/request")),
            'order_item.sku = returns.product_sku and main_table.entity_id = returns.order_id and approved = 1',
            array()
        );
        $collection->addFieldToFilter("main_table.state",Mage_Sales_Model_Order::STATE_COMPLETE);
        $collection->getSelect()->group(array("order_item.sku","order_item.base_original_price"));
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $collection->getSelect()->columns(array(
            "name"=> "order_item.name",
            "author"=> "author.value",
            "isbn"=> "order_item.sku",
            "price"=> "order_item.base_original_price",
            "qty"=>new Zend_Db_Expr("SUM(order_item.qty_ordered)"),
            "returns"=>new Zend_Db_Expr("SUM(returns.id)"),
            "total"=>new Zend_Db_Expr("SUM(order_item.base_row_total)"),
        ));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('name', array(
            'header'    => Mage::helper('entangled_reports')->__('Name'),
            'index'     =>'name',
            'type'      =>'text',
            "filter" => false,
            "sortable" => false,
        ));
        $this->addColumn('author_names', array(
            'header'    => Mage::helper('entangled_reports')->__('Authors Name'),
            'index'     =>'author',
            'type'      =>'text',
            'renderer'  => 'entangled_reports/adminhtml_books_grid_renderer_authors',
            "filter" => false,
            "sortable" => false,
            "width" => "400px"
        ));
        $this->addColumn('isbn', array(
            'header'    => Mage::helper('entangled_reports')->__('ISBN'),
            'index'     =>'isbn',
            'type'      =>'text',
            "filter" => false,
            "sortable" => false,
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('entangled_reports')->__('Price'),
            'index'     =>'price',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
            "filter" => false,
            "sortable" => false,

        ));
        $this->addColumn('qty', array(
            'header'    => Mage::helper('entangled_reports')->__('QTY'),
            'index'     =>'qty',
            'type'      =>'number',
            "filter" => false,
            "sortable" => false,
        ));
        $this->addColumn('returns', array(
            'header'    => Mage::helper('entangled_reports')->__('Returns'),
            'index'     =>'returns',
            'type'      =>'number',
            "filter" => false,
            "sortable" => false,
        ));
        $this->addColumn('total', array(
            'header'    => Mage::helper('entangled_reports')->__('Total gross sales'),
            'index'     =>'total',
            'type'  => 'currency',
            'currency' => 'base_currency_code',
            "filter" => false,
            "sortable" => false,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('entangled_reports')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('entangled_reports')->__('XML'));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return false;
    }

}