<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Customer_Edit_Tab_CustomerCredit_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('creditLogGrid');
        $this->setDefaultSort('action_date');
        $this->setUseAjax(true);
    }

    public function getWebsiteOptions() {
        $options = Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash();        
        $options[0] = Mage::helper('mageworx_customercredit')->__('Global');
        return $options;
    }    
    
    protected function _prepareColumns() {
        $helper = Mage::helper('mageworx_customercredit');
        $this->addColumn('value', array(
            'header'    => $helper->__('Credit Balance'),
            'index'     => 'value',
            'sortable'  => false,
            'filter'    => false,
            'width'     => '50px',
            'renderer'  => 'mageworx_customercredit/adminhtml_widget_grid_column_renderer_currency'
        ));
        $this->addColumn('value_change', array(
            'header'    => $helper->__('Added/Deducted'),
            'index'     => 'value_change',
            'sortable'  => false,
            'filter'    => false,
            'width'     => '50px',
            'renderer'  => 'mageworx_customercredit/adminhtml_widget_grid_column_renderer_currencychange'
        ));
        $this->addColumn('website_id', array(
            'header'    => Mage::helper('mageworx_customercredit')->__('Website'),
            'index'     => 'website_id',
            'type'      => 'options',
            'options'   => $this->getWebsiteOptions(),
            'sortable'  => false,
            'width'     => '120px',
            'filter_index'=>'main_table.website_id',
            'align'      => 'center'
        ));
        $this->addColumn('action_date', array(
            'header'   => $helper->__('Modified On'),
            'index'    => 'action_date',
            'type'     => 'datetime',
            'width'    => '160px',
            'filter'   => false,
        ));
        $this->addColumn('action_type', array(
            'header'    => Mage::helper('mageworx_customercredit')->__('Action'),
            'width'     => '70px',
            'index'     => 'action_type',
            'sortable'  => false,
            'type'      => 'options',
            'options'   => Mage::getSingleton('mageworx_customercredit/credit_log')->getActionTypesOptions(),
        ));
        $this->addColumn('comment', array(
            'header'    => $helper->__('Comment'),
            'index'     => 'comment',
            'type'      => 'text',
            'nl2br'     => true,
            'sortable'  => false,
            'filter'   => false,
        ));
        
        return parent::_prepareColumns();
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mageworx_customercredit/credit_log')
            ->getCollection()
            ->addCustomerFilter(Mage::registry('current_customer')->getId());

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/logGrid', array('_current'=> true));
    }
}