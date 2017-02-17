<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Rules_Edit_Tab_Actions extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_customercredit_rule');
	
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('mageworx_customercredit')->__('Update prices using the following information')));
	$rule_type = MageWorx_CustomerCredit_Model_Rules::CC_RULE_TYPE_APPLY;
        if(Mage::registry('current_customercredit_rule')->getId()) {
            $rule_type = Mage::registry('current_customercredit_rule')->getRuleType();
        }
        if(Mage::app()->getRequest()->getParam('current_rule_type')) {
            $rule_type = Mage::app()->getRequest()->getParam('current_rule_type');
        }

        $fieldset->addField('is_onetime', 'select', array(
            'label'     => Mage::helper('mageworx_customercredit')->__('One-time'),
            'name'      => 'is_onetime',
            'disabled'  => ($rule_type == MageWorx_CustomerCredit_Model_Rules::CC_RULE_TYPE_APPLY) ? true : false,
            'note'   => ($rule_type == MageWorx_CustomerCredit_Model_Rules::CC_RULE_TYPE_APPLY) ? "<span class='disabled'>".Mage::helper('mageworx_customercredit')->__('Disabled')."<span>" : '',
            'options'   => Mage::getModel('adminhtml/system_config_source_yesno')->toArray(),
        ));

        $fieldset->addField('qty_dependent', 'select', array(
            'label'     => Mage::helper('mageworx_customercredit')->__('Qty Dependent'),
            'name'      => 'qty_dependent',
            'disabled'  => ($rule_type == MageWorx_CustomerCredit_Model_Rules::CC_RULE_TYPE_APPLY) ? true : false,
            'note'   => ($rule_type == MageWorx_CustomerCredit_Model_Rules::CC_RULE_TYPE_APPLY) ? "<span class='disabled'>".Mage::helper('mageworx_customercredit')->__('Disabled')."<span>" : '',
            'options'   => array(
                '1' => Mage::helper('mageworx_customercredit')->__('Yes'),
                '0' => Mage::helper('mageworx_customercredit')->__('No')
            ),
        ));
                
        $element = $fieldset->addField('credit', 'text', array(
            'name'      => 'credit',
            'required'  => true,
            'disabled'  => ($rule_type == MageWorx_CustomerCredit_Model_Rules::CC_RULE_TYPE_APPLY) ? true : false,
            'note'   => ($rule_type == MageWorx_CustomerCredit_Model_Rules::CC_RULE_TYPE_APPLY) ? "<span class='disabled'>".Mage::helper('mageworx_customercredit')->__('Disabled')."<span>" : '',
            'class'     => 'validate-not-negative-number',
            'label'     => Mage::helper('mageworx_customercredit')->__('Credit Amount'),
        ));

        $element->setAfterElementHtml('<style>.disabled {color:red; font-weight:bold;}</style>');
        $form->setValues($model->getData());
		
        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
