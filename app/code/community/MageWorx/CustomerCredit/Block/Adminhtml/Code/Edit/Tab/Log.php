<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Code_Edit_Tab_Log extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setUseAjax(true);
        $this->setDefaultSort('action_date');
        $this->setDefaultDir('desc');
        $this->setId('logGrid');
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mageworx_customercredit/code_log')
            ->getCollection()
            ->addCodeFilter(Mage::registry('current_customercredit_code')->getId());
        $this->setCollection($collection);
            
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('action_date', array(
            'header'    => $this->_helper()->__('Modified On'),
            'index'     => 'action_date',
            'type'      => 'datetime',
            'width'     => 150,
        ));
        $this->addColumn('action_type', array(
            'header'    => $this->_helper()->__('Action'),
            'index'     => 'action_type',
            'type'      => 'options',
            'width'     => 130,
            'sortable'  => false,
            'options'   => Mage::getSingleton('mageworx_customercredit/code_log')->getActionTypesOptions(),
        ));
        $this->addColumn('credit', array(
            'header'    => $this->_helper()->__('Credit'),
            'index'     => 'credit',
            'type'      => 'price',
            'width'     => 100,
            'sortable'  => false,
            'filter'    => false,
            'currency_code' => Mage::app()->getWebsite(Mage::registry('current_customercredit_code')->getWebsiteId())->getBaseCurrencyCode(),
        ));
        $this->addColumn('comment', array(
            'header'    => $this->_helper()->__('Comment'),
            'index'     => 'comment',
            'sortable'  => false,
        ));
        
        return parent::_prepareColumns();
    }
    
    /**
     * 
     * @return MageWorx_CustomerCredit_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('mageworx_customercredit');
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/logGrid', array('_current'=> true));
    }
}