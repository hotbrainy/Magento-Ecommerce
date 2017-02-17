<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
 
class MageWorx_CustomerCredit_Block_Adminhtml_Code_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
    {
        parent::__construct();
        $this->setId('customercredit_code_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('mageworx_customercredit')->__('Recharge Code'));
    }
    
    protected function _beforeToHtml()
    {
        $codeModel = Mage::registry('current_customercredit_code');
        if ($codeModel->getIsNew())
        {
            $this->addTab('settings_section', array(
                'label'     => Mage::helper('mageworx_customercredit')->__('Settings'),
                'title'     => Mage::helper('mageworx_customercredit')->__('Settings'),
                'content'   => $this->getLayout()->createBlock('mageworx_customercredit/adminhtml_code_edit_tab_settings')->toHtml(),
                'active'    => true,
            ));
        }
        $this->addTab('details_section', array(
            'label'     => Mage::helper('mageworx_customercredit')->__('Details'),
            'title'     => Mage::helper('mageworx_customercredit')->__('Details'),
            'content'   => $this->getLayout()->createBlock('mageworx_customercredit/adminhtml_code_edit_tab_details')->toHtml(),
            'active'    => $codeModel->getIsNew() ? false : true
        ));

        if (!$codeModel->getIsNew())
        {
            $this->addTab('log_section', array(
                'label'     => Mage::helper('mageworx_customercredit')->__('Action Log'),
                'title'     => Mage::helper('mageworx_customercredit')->__('Action Log'),
                'content'   => $this->getLayout()->createBlock('mageworx_customercredit/adminhtml_code_edit_tab_log')->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }
}