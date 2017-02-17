<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Rules_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customercredit_rules_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('mageworx_customercredit')->__('Loyalty Booster Rules'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => Mage::helper('mageworx_customercredit')->__('Rule Information'),
            'content'   => $this->getLayout()->createBlock('mageworx_customercredit/adminhtml_rules_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('conditions_section', array(
            'label'     => Mage::helper('mageworx_customercredit')->__('Conditions'),
            'content'   => $this->getLayout()->createBlock('mageworx_customercredit/adminhtml_rules_edit_tab_conditions')->toHtml(),
//            'url'   => $this->getUrl('*/*/getConditions', array('_current' => true)),
//            'class' => 'ajax',
        ));

        $this->addTab('actions_section', array(
            'label'     => Mage::helper('mageworx_customercredit')->__('Actions'),
            'content'   => $this->getLayout()->createBlock('mageworx_customercredit/adminhtml_rules_edit_tab_actions')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }

}
