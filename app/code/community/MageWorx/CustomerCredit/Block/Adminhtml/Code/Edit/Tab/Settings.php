<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Code_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_sectionCode      = 'mageworx_customercredit';
    protected $_sectionGroupCode = 'recharge_codes';
    
    protected function _prepareForm()
    {
        $model = Mage::registry('current_customercredit_code');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('code_settings_');
        $form->setFieldNameSuffix('settings');
        
        $fieldset = $form->addFieldset('settings_fieldset', array('legend'=>$this->_helper()->__('Settings')));
        foreach ($this->_getSettingsFields() as $code => $field)
        {
            if (!empty($field['frontend_model']))
            {
                $fieldRenderer = Mage::getBlockSingleton((string)$field['frontend_model']);
            }
            else 
            {
                $fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
            }
            $fieldRenderer->setForm($this);
            
            $fieldConfig = array(
                'name'      => $code,
                'label'     => $field['label'],
                'title'     => $field['label'],
                'comment'   => !empty($field['comment']) ? (string)$field['comment'] : '',
                'class'     => !empty($field['validate']) ? $field['validate'] : '',
                //'default'   => $field['value'],
            );
            if ($field['frontend_type'] == 'select' && !empty($field['source_model']))
            {
                $fieldConfig['values'] = Mage::getSingleton($field['source_model'])->toOptionArray();
            }
            $checked  = ' checked="checked" ';
            $checkboxLabel = 'Use Config Settings';
            $defText = Mage::getStoreConfig($this->_sectionCode.'/'.$this->_sectionGroupCode.'/'.$code);
            $html = '<input id="'.$code.'_use_config" name="use_config['.$code.']" type="checkbox" value="1" class="checkbox" '.$checked.' onclick="toggleValueElements(this, this.parentNode)" /> ';
            $html.= '<label for="'.$code.'_use_config" title="'.htmlspecialchars($defText).'">'.$checkboxLabel.'</label>';
            $fieldConfig['after_element_html'] = $html;
            
            $formField  = $fieldset->addField($code, $field['frontend_type'], $fieldConfig);
            $formField->setDisabled(true);
            $formField->setRenderer($fieldRenderer);
        }
        $form->setValues(Mage::getStoreConfig($this->_sectionCode.'/'.$this->_sectionGroupCode));
        $formField  = $fieldset->addField('qty', 'text', array(
            'name'      => 'qty',
            'label'     => $this->_helper()->__('Qty'),
            'title'     => $this->_helper()->__('Qty'),
            'class'     => 'validate-number validate-greater-than-zero',
            'required'  => true,
            'value'   => 1,
        ));
        $form->addValues($model->getData());
        
        $this->setForm($form);
        return parent::_prepareForm();
    }
    
    protected function _getSettingsFields()
    {
        
        $data = Mage::getStoreConfig($this->_sectionCode.'/'.$this->_sectionGroupCode);
        $configFields = Mage::getSingleton('adminhtml/config');
        /* @var $configFields Mage_Adminhtml_Model_Config */
        
        $section = $configFields->getSection($this->_sectionCode);
        $fields = array();
        if (isset($section->groups->{$this->_sectionGroupCode}->fields) && $section->groups->{$this->_sectionGroupCode}->fields->hasChildren())
        {
            $fields = $section->groups->{$this->_sectionGroupCode}->fields->asArray();
            foreach ($fields as $k=>$field)
            {
                $fields[$k]['value'] = $data[$k];
            }
        }
        return $fields;
    }
    
    /**
     * 
     * @return MageWorx_CustomerCredit_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('mageworx_customercredit');
    }
}