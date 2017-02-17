<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_report';
		$this->_blockGroup = 'mageworx_customercredit';
		$this->_headerText = Mage::helper('mageworx_customercredit')->__('Loyalty Booster Reports');
	//	$this->_addButtonLabel = Mage::helper('mageworx_customercredit')->__('Add New Credit Rule');
		parent::__construct();
                $this->_removeButton('add');
                
	}
        
//        protected function _prepareLayout() {
//            $this->setChild( 'total',
//            $this->getLayout()->createBlock( $this->_blockGroup.'/' . $this->_controller . '_total',
//            $this->_controller . '.total')->setSaveParametersInSession(true) );
//            parent::_prepareLayout();
//        }
}
