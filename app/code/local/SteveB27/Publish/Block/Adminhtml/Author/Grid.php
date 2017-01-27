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

class SteveB27_Publish_Block_Adminhtml_Author_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('authorGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    protected function _prepareCollection() {
        $collection = Mage::getModel('publish/author')->getCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('publish_date')
            ->addAttributeToSelect('status')
            ->addAttributeToSelect('url_key');
        $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
        $store = $this->_getStore();
     //   $collection->joinAttribute('name', 'author/name', 'entity_id', null, 'inner', $adminStore);
        if ($store->getId()) {
            $collection->joinAttribute('publish_author_name', 'publish_author/name', 'entity_id', null, 'inner', $store->getId());
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('publish')->__('Id'),
            'index'        => 'entity_id',
            'type'        => 'number'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('publish')->__('Name'),
            'align'     => 'left',
            'index'     => 'name',
        ));
        if ($this->_getStore()->getId()){
            $this->addColumn('publish_author_name', array(
                'header'    => Mage::helper('publish')->__('Name in %s', $this->_getStore()->getName()),
                'align'     => 'left',
                'index'     => 'publish_author_name',
            ));
        }

        $this->addColumn('status', array(
            'header'    => Mage::helper('publish')->__('Status'),
            'index'        => 'status',
            'type'        => 'options',
            'options'    => array(
                '1' => Mage::helper('publish')->__('Enabled'),
                '0' => Mage::helper('publish')->__('Disabled'),
            )
        ));
        $this->addColumn('url_key', array(
            'header' => Mage::helper('publish')->__('URL key'),
            'index'  => 'url_key',
        ));
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('publish')->__('Created at'),
            'index'     => 'created_at',
            'width'     => '120px',
            'type'      => 'datetime',
        ));
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('publish')->__('Updated at'),
            'index'     => 'updated_at',
            'width'     => '120px',
            'type'      => 'datetime',
        ));
        $this->addColumn('action',
            array(
                'header'=>  Mage::helper('publish')->__('Action'),
                'width' => '100',
                'type'  => 'action',
                'getter'=> 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('publish')->__('Edit'),
                        'url'   => array('base'=> '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter'=> false,
                'is_system'    => true,
                'sortable'  => false,
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('publish')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('publish')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('publish')->__('XML'));
        return parent::_prepareColumns();
    }

    protected function _getStore(){
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _prepareMassaction(){
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('author');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('publish')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('publish')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('publish')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'status' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('publish')->__('Status'),
                        'values' => array(
                                '1' => Mage::helper('publish')->__('Enabled'),
                                '0' => Mage::helper('publish')->__('Disabled'),
                        )
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row){
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    public function getGridUrl(){
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
}