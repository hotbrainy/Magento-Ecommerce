<?php
/**
 * Custom Publisher Models
 * 
 * Add custom model types, such as author, which can be used as a product
 * attribute while proviting additional details.
 * 
 * @license 	http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 * @copyright	Steven Brown March 12, 2016
 * @author		Steven Brown <steveb.27@outlook.com>
 */

class SteveB27_Publish_Block_Adminhtml_Attribute_Edit_Tab_Main extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
{
    protected function _prepareForm(){
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        $form = $this->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $frontendInputElm = $form->getElement('frontend_input');
        $additionalTypes = array(
            array(
                'value' => 'image',
                'label' => Mage::helper('publish')->__('Image')
            ),
            array(
                'value' => 'file',
                'label' => Mage::helper('publish')->__('File')
            )
        );
        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('publish_adminhtml_attribute_types', array('response'=>$response));
        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $additionalTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }
        Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        Mage::register('attribute_type_disabled_types', $_disabledTypes);

        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues);

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $scopes = array(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('publish')->__('Store View'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('publish')->__('Website'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('publish')->__('Global'),
        );

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => Mage::helper('publish')->__('Scope'),
            'title' => Mage::helper('publish')->__('Scope'),
            'note'  => Mage::helper('publish')->__('Declare attribute value saving scope'),
            'values'=> $scopes
        ), 'attribute_code');
        $fieldset->addField('position', 'text', array(
            'name'  => 'position',
            'label' => Mage::helper('publish')->__('Position'),
            'title' => Mage::helper('publish')->__('Position'),
            'note'  => Mage::helper('publish')->__('Position in the admin form'),
        ), 'is_global');
        $fieldset->addField('note', 'textarea', array(
            'name'  => 'note',
            'label' => Mage::helper('publish')->__('Note'),
            'title' => Mage::helper('publish')->__('Note'),
            'note'  => Mage::helper('publish')->__('Text to appear below the input.'),
        ), 'position');

        $fieldset->removeField('default_value_text');
        $fieldset->removeField('default_value_yesno');
        $fieldset->removeField('default_value_date');
        $fieldset->removeField('default_value_textarea');
        $fieldset->removeField('is_unique');
        // frontend properties fieldset
     //   $fieldset = $form->addFieldset('front_fieldset', array('legend'=>Mage::helper('publish')->__('Frontend Properties')));


        $fieldset->addField('is_wysiwyg_enabled', 'select', array(
            'name' => 'is_wysiwyg_enabled',
            'label' => Mage::helper('publish')->__('Enable WYSIWYG'),
            'title' => Mage::helper('publish')->__('Enable WYSIWYG'),
            'values' => $yesnoSource,
        ));
        Mage::dispatchEvent('publish_adminhtml_attribute_edit_prepare_form', array(
            'form'      => $form,
            'attribute' => $attributeObject
        ));
        return $this;
    }
}