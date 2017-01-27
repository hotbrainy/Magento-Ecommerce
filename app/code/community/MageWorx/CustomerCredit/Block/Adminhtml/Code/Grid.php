<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
 
class MageWorx_CustomerCredit_Block_Adminhtml_Code_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
            parent::__construct();
            $this->setId('codeGrid');
            $this->setSaveParametersInSession(true);
            $this->setDefaultSort('code_id');
            $this->setDefaultDir('desc');
            $this->setUseAjax(true);
            $this->setVarNameFilter('customercredit_code_filter');
	}
	
	protected function _prepareColumns()
	{
		$this->addColumn('code_id',
            array(
                'header'=> $this->_helper()->__('ID'),
                'width' => '50px',
                'index' => 'code_id',
        ));
        $this->addColumn('code',
            array(
                'header'=> $this->_helper()->__('Code'),
                'width' => '250px',
                'index' => 'code',
        ));
        $this->addColumn('credit',
            array(
                'header'=> $this->_helper()->__('Value'),
                'width' => '80px',
                'type'  => 'number',
            	//'currency_code' => Mage::app()->getStore()->getBaseCurrency()->getCode(),
                'index' => 'credit',
                'renderer' => 'mageworx_customercredit/adminhtml_widget_grid_column_renderer_currency'
        ));
        $this->addColumn('website_id',
            array(
                'header'=> $this->_helper()->__('Website'),
                'type'  => 'options',
                'index' => 'website_id',
            	'options' => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
        ));
        /*$this->addColumn('created_date',
            array(
                'header'=> $this->_helper()->__('Date Created'),
                'width' => '50px',
                'type'  => 'date',
                'index' => 'created_date',
        ));*/
        $this->addColumn('from_date', 
            array(
                'header'    => $this->_helper()->__('Date Start'),
                'align'     => 'left',
                'width'     => '50px',
                'type'      => 'date',
                'index'     => 'from_date',
        ));

        $this->addColumn('to_date',
            array(
                'header'    => $this->_helper()->__('Date Expire'),
                'align'     => 'left',
                'width'     => '50px',
                'type'      => 'date',
                'default'   => '--',
                'index'     => 'to_date',
        ));
        $this->addColumn('used_date',
            array(
                'header'=> $this->_helper()->__('Last Used'),
                'width' => '50px',
                'type'  => 'date',
                'index' => 'used_date',
            	'default' => '-',
        ));
        $this->addColumn('is_active',
            array(
                'header'=> $this->_helper()->__('Is Active'),
                'width' => 30,
                'type'  => 'options',
                'index' => 'is_active',
            	'options' => array_reverse(Mage::getModel('adminhtml/system_config_source_yesno')->toArray()),
        ));
        $this->addColumn('is_onetime',
            array(
                'header'=> $this->_helper()->__('Is Onetime'),
                'width' => 30,
                'type'  => 'options',
                'index' => 'is_onetime',
            	'options' => array_reverse(Mage::getModel('adminhtml/system_config_source_yesno')->toArray()),
        ));
        $this->addColumn('owner_id',
            array(
                'header'=> $this->_helper()->__('Owner ID'),
                'width' => '50px',
                'type'  => 'int',
                'index' => 'owner_id',
            	'default' => '--',
        ));
        
        
        return parent::_prepareColumns();
	}
	
	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('mageworx_customercredit/code_collection');
		$this->setCollection($collection);
        return parent::_prepareCollection();
	}
	
	public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    
	public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id'    => $row->getId()
        ));
    }
    
    protected function _helper()
    {
    	return Mage::helper('mageworx_customercredit');
    }
}