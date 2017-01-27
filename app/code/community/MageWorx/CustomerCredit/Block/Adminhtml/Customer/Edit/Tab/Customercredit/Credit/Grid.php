<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Customer_Edit_Tab_CustomerCredit_Credit_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() 
    {
        parent::__construct();
        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);
        $this->setId('creditGrid');
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('website_id', array(
            'header'   => Mage::helper('mageworx_customercredit')->__('Website'),
            'index'    => 'website_id',
            'type'     => 'options',
            'options'  => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
            'width'    => 250,
            'sortable' => false,
        ));
        
        $this->addColumn('value', array(
            'header'   => Mage::helper('mageworx_customercredit')->__('Credit'),
            'index'    => 'value',
            'sortable' => false,
        ));
        
        return parent::_prepareColumns();
    }
    
    protected function _prepareCollection()
    {
        $customer = Mage::registry('current_customer');
        $collection = Mage::getResourceModel('mageworx_customercredit/credit_collection')
            ->addCustomerFilter($customer->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
}