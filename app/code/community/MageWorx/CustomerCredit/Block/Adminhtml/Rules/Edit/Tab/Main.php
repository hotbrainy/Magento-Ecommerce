<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Rules_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('current_customercredit_rule');
        $helper = Mage::helper('mageworx_customercredit');
       	$form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('mageworx_customercredit')->__('General Information')));

        if ($model->getId()) {
            $fieldset->addField('rule_id', 'hidden', array(
                'name' => 'rule_id',
            ));
        };

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => $helper->__('Rule Name'),
            'title' => $helper->__('Rule Name'),
            'required' => true,
        ));

        $fieldset->addField('description', 'textarea', array(
            'name' => 'description',
            'label' => $helper->__('Description'),
            'title' => $helper->__('Description'),
            'style' => 'width: 98%; height: 100px;',
        ));

        $fieldset->addField('is_active', 'select', array(
            'label'     => $helper->__('Status'),
            'title'     => $helper->__('Status'),
            'name'      => 'is_active',
            'required' => true,
            'options'    => array(
                '1' => $helper->__('Active'),
                '0' => $helper->__('Inactive'),
            ),
        ));
        $options = Mage::getModel('mageworx_customercredit/rules')->getRuleTypeArray();
        $fieldset->addField('rule_type', 'select', array(
            'label'     => $helper->__('Type'),
            'title'     => $helper->__('Type'),
            'name'      => 'rule_type',
            'required'  => true,
            'options'   => $options,
            'note'      => $helper->__('Note: existing conditions will be removed after changing the rule’s type.'),
            'onchange'  => "rendererConditions(this,'".$this->getUrl('*/*/getConditions', array('_current' => true))."'); rendererActions(this,'".$this->getUrl('*/*/getActions', array('_current' => true))."')",
        ));
        $options = Mage::getSingleton('mageworx_customercredit/system_config_source_email_template')->getOptionArray();
        $fieldset->addField('email_template', 'select', array(
            'label'     => $helper->__('Email Template'),
            'title'     => $helper->__('Email Template'),
            'name'      => 'email_template',
            'required'  => true,
            'options'   => $options,
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website_ids', 'multiselect', array(
                'name'      => 'website_ids[]',
                'label'     => $helper->__('Websites'),
                'title'     => $helper->__('Websites'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_config_source_website')->toOptionArray(),
            ));
        }
        else {
            $fieldset->addField('website_ids', 'hidden', array(
                'name'      => 'website_ids[]',
                'value'     => Mage::app()->getStore(true)->getWebsiteId()
            ));
            $model->setWebsiteIds(Mage::app()->getStore(true)->getWebsiteId());
        }

        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->load()->toOptionArray();
	  foreach ($customerGroups as $key => $group){
        	if($group['value'] == '0'){
        		unset($customerGroups[$key]);
        	}
        }

        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'      => 'customer_group_ids[]',
            'label'     => $helper->__('Customer Groups'),
            'title'     => $helper->__('Customer Groups'),
            'required'  => true,
            'values'    => $customerGroups,
        ));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
