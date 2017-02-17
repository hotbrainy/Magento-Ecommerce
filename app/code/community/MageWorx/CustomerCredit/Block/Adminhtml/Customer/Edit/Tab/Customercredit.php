<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Customer_Edit_Tab_CustomerCredit
 extends Mage_Adminhtml_Block_Template
 implements Mage_Adminhtml_Block_Widget_Tab_Interface {
    
    public function __construct() {
        parent::__construct();
        $this->setId('customercredit_credit');
    }

    public function getAfter() {
        return 'tags';
    }

    public function getTabLabel() {
        return Mage::helper('mageworx_customercredit')->__('Internal Credit');
    }

    public function getTabTitle() {
        return Mage::helper('mageworx_customercredit')->__('Internal Credit');
    }

    public function canShowTab() {
        return true;
    }

    public function isHidden() {
        if (!Mage::helper('mageworx_customercredit')->isShowCustomerCredit()) {
            return true;
        }
        if (Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }

    protected function _toHtml() {
        return parent::_toHtml() . $this->getChildHtml();
    }
}