<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Rules extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_rules';
		$this->_blockGroup = 'mageworx_customercredit';
		$this->_headerText = Mage::helper('mageworx_customercredit')->__('Manage Credit Rules');
		$this->_addButtonLabel = Mage::helper('mageworx_customercredit')->__('Add New Credit Rule');
		
		parent::__construct();
	}
}
