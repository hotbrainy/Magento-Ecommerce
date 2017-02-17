<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Rules_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'mageworx_customercredit';
        $this->_controller = 'adminhtml_rules';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('mageworx_customercredit')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('mageworx_customercredit')->__('Delete Rule'));

        $rule = $this->getRule();

        #$this->setTemplate('promo/quote/edit.phtml');
    }
    
    public function getRule(){
    	return Mage::registry('current_customercredit_rule');
    }

    public function getHeaderText()
    {
        $rule = $this->getRule();
        if ($rule->getRuleId()) {
            return Mage::helper('mageworx_customercredit')->__("Edit Rule '%s'", $this->htmlEscape($rule->getName()));
        }
        else {
            return Mage::helper('mageworx_customercredit')->__('New Rule');
        }
    }

    public function getProductsJson()
    {
        return '{}';
    }
}
