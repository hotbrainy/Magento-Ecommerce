<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Rules_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('current_customercredit_rule');
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/mageworx_customercredit_rules/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('mageworx_customercredit')->__('Apply the rule only if the following conditions are met (leave blank for all products)')
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('mageworx_customercredit')->__('Apply to'),
            'title' => Mage::helper('mageworx_customercredit')->__('Apply to'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('mageworx_customercredit/rules_conditions'));

        if (Mage::app()->getRequest()->getParam('current_rule_type') != $model->getRuleType()) {
            $model->setConditionsSerialized();
        }

        $form->setValues($model->getData());

        //$form->setUseContainer(true);
		
        $this->setForm($form);

        return parent::_prepareForm();
    }
}