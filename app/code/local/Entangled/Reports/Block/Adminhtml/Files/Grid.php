<?php
class Entangled_Reports_Block_Adminhtml_Files_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('booksFilesGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setSubReportSize(false);
    }

    protected function _prepareCollection() {

        $authorAttribute = Mage::getResourceModel('eav/entity_attribute')->getIdByCode('catalog_product','publish_author');
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getModel("catalog/product")->getCollection();
        $resource = $collection->getResource();

        $collection->addAttributeToFilter("type_id",Mage_Downloadable_Model_Product_Type::TYPE_DOWNLOADABLE);
        $collection->addAttributeToSelect(array("name","sku"));

        $alias = $collection->isEnabledFlat() ? "main_table" : "e";

        $collection->getSelect()->joinLeft(array('mobi_file'=> $resource->getTable("downloadable/link")), 'mobi_file.product_id = '.$alias.'.entity_id AND mobi_file.link_file LIKE "%.mobi%"', array());
        $collection->getSelect()->joinLeft(array('epub_file'=> $resource->getTable("downloadable/link")), 'epub_file.product_id = '.$alias.'.entity_id AND epub_file.link_file LIKE "%.epub%"', array());
        $collection->getSelect()->joinLeft(array('pdf_file'=> $resource->getTable("downloadable/link")), 'pdf_file.product_id = '.$alias.'.entity_id AND pdf_file.link_file LIKE "%.pdf%"', array());

        //$collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $collection->getSelect()->columns(array(
            "mobi"=> "mobi_file.link_file",
            "epub"=> "epub_file.link_file",
            "pdf"=> "pdf_file.link_file",
        ));

        $this->setCollection($collection);
        $sql = (string)$collection->getSelect();

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
        $this->addColumn('isbn', array(
            'header'    => Mage::helper('entangled_reports')->__('ISBN'),
            'index'     =>'sku',
            'type'      =>'text',
            "filter" => false,
            "sortable" => false,
        ));
        $this->addColumn('epub', array(
            'header'    => Mage::helper('entangled_reports')->__('EPUB'),
            'index'     =>'epub',
            'type'      =>'text',
            "filter" => false,
        ));
        $this->addColumn('pdf', array(
            'header'    => Mage::helper('entangled_reports')->__('PDF'),
            'index'     =>'pdf',
            'type'      =>'text',
            "filter" => false,
        ));
        $this->addColumn('mobi', array(
            'header'    => Mage::helper('entangled_reports')->__('MOBI'),
            'index'     =>'mobi',
            'type'      =>'text',
            "filter" => false,
        ));

        if (!isset($_SERVER['REQUEST_METHOD'])) {
            $this->addColumn('pdf_test', array(
                'header'    => Mage::helper('entangled_reports')->__('PDF Working'),
                'index'     =>'pdf',
                'type'      =>'text',
                'renderer'  => 'entangled_reports/adminhtml_files_grid_renderer_pdfTest',
                "filter" => false,
                "sortable" => false,
            ));
        }

        $this->addExportType('*/*/exportFilesCsv', Mage::helper('entangled_reports')->__('CSV'));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return false;
    }

}