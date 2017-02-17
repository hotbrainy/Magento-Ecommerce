<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Code_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
	public function __construct()
    {
    	$this->_controller = 'adminhtml_code';
		$this->_blockGroup = 'mageworx_customercredit';
        parent::__construct();
        if ($this->getCode()->getIsNew())
            $this->_updateButton('save', 'label', Mage::helper('mageworx_customercredit')->__('Generate'));
    }
    
    /**
     * 
     * @return MageWorx_CustomerCredit_Model_Code
     */
    public function getCode()
    {
    	return Mage::registry('current_customercredit_code');
    }
    
    public function getHeaderText()
    {
    	if (!$this->getCode()->getIsNew()) {
            return Mage::helper('mageworx_customercredit')->__('Edit Recharge Code: %s', $this->htmlEscape($this->getCode()->getCode()));
        }
        else {
            return Mage::helper('mageworx_customercredit')->__('Generate Recharge Codes');
        }
    }
}