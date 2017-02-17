<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Block_Adminhtml_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $model = Mage::registry('attributemanager_data');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('catalog')->__('Attribute Properties'))
        );
        if ($model->getId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $this->_addElementTypes($fieldset);

        $yesno = array(
            array(
                'value' => 0,
                'label' => Mage::helper('catalog')->__('No')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('catalog')->__('Yes')
            ));

        $fieldset->addField('attribute_code', 'text', array(
            'name'  => 'attribute_code',
            'label' => Mage::helper('catalog')->__('Attribute Code'),
            'title' => Mage::helper('catalog')->__('Attribute Code'),
            'note'  => Mage::helper('catalog')->__('For internal use. Must be unique with no spaces'),
            'class' => 'validate-code',
            'required' => true,
        ));

        $scopes = array(
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('catalog')->__('Store View'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('catalog')->__('Website'),
            Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('catalog')->__('Global'),
        );

        if ($model->getAttributeCode() == 'status' || $model->getAttributeCode() == 'tax_class_id') {
            unset($scopes[Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE]);
        }

        $inputTypes = array(
            array(
                'value' => 'text',
                'label' => Mage::helper('catalog')->__('Text Field')
            ),
            array(
                'value' => 'textarea',
                'label' => Mage::helper('catalog')->__('Text Area')
            ),
//            array(
//                'value' => 'date',
//                'label' => Mage::helper('catalog')->__('Date')
//            ),
            array(
                'value' => 'boolean',
                'label' => Mage::helper('catalog')->__('Yes/No')
            ),
            array(
                'value' => 'multiselect',
                'label' => Mage::helper('catalog')->__('Multiple Select')
            ),
            array(
                'value' => 'select',
                'label' => Mage::helper('catalog')->__('Dropdown')
            ),
        );
        if($this->getRequest ()->getParam ( 'type' )==="catalog_category"){
            $inputTypes[]=   array(
                'value' => 'image',
                'label' => Mage::helper('catalog')->__('Image')

            );
        }

        $response = new Varien_Object();
        $response->setTypes(array());

        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $inputTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }
        Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        Mage::register('attribute_type_disabled_types', $_disabledTypes);


        $fieldset->addField('frontend_input', 'select', array(
            'name' => 'frontend_input',
            'label' => Mage::helper('catalog')->__('Input Type'),
            'title' => Mage::helper('catalog')->__('Input Type'),
            'value' => 'text',
            'values'=> $inputTypes
        ));

        $fieldset->addField('entity_type_id', 'hidden', array(
            'name' => 'entity_type_id',
            'value' => Mage::getModel('eav/entity')->setType($this->getRequest ()->getParam ( 'type' ))->getTypeId()
        ));



        $fieldset->addField('is_user_defined', 'hidden', array(
            'name' => 'is_user_defined',
            'value' => 1
        ));

        $fieldset->addField('attribute_set_id', 'hidden', array(
            'name' => 'attribute_set_id',
            'value' => Mage::getModel('eav/entity')->setType($this->getRequest ()->getParam ( 'type' ))->getTypeId()
        ));

        $fieldset->addField('attribute_group_id', 'hidden', array(
            'name' => 'attribute_group_id',
            'value' => Mage::getModel('eav/entity')->setType($this->getRequest ()->getParam ( 'type' ))->getTypeId()
        ));


        /*******************************************************/
        $fieldset->addField('is_required', 'select', array(
            'name' => 'is_required',
            'label' => Mage::helper('catalog')->__('Values Required'),
            'title' => Mage::helper('catalog')->__('Values Required'),
            'values' => $yesno,
        ));

        $fieldset->addField('frontend_class', 'select', array(
            'name'  => 'frontend_class',
            'label' => Mage::helper('catalog')->__('Input Validation'),
            'title' => Mage::helper('catalog')->__('Input Validation'),
            'values'=>  array(
                array(
                    'value' => '',
                    'label' => Mage::helper('catalog')->__('None')
                ),
                array(
                    'value' => 'validate-number',
                    'label' => Mage::helper('catalog')->__('Decimal Number')
                ),
                array(
                    'value' => 'validate-digits',
                    'label' => Mage::helper('catalog')->__('Integer Number')
                ),
                array(
                    'value' => 'validate-email',
                    'label' => Mage::helper('catalog')->__('Email')
                ),
                array(
                    'value' => 'validate-url',
                    'label' => Mage::helper('catalog')->__('Url')
                ),
                array(
                    'value' => 'validate-alpha',
                    'label' => Mage::helper('catalog')->__('Letters')
                ),
                array(
                    'value' => 'validate-alphanum',
                    'label' => Mage::helper('catalog')->__('Letters(a-zA-Z) or Numbers(0-9)')
                ),
            )
        ));

        // -----


        // frontend properties fieldset
        $fieldset = $form->addFieldset('front_fieldset',
            array('legend'=>Mage::helper('catalog')->__('Frontend Properties')));



        $fieldset->addField('sort_order', 'text', array(
            'name' => 'sort_order',
            'label' => Mage::helper('catalog')->__('Order'),
            'title' => Mage::helper('catalog')->__('Order in form'),
            'note' => Mage::helper('catalog')->__('order of attribute in form edit/create. Leave blank for form bottom.'),
            'class' => 'validate-digits',
            'value' => $model->getSortOrder()
        ));


        if ($model->getId()) {
            $form->getElement('attribute_code')->setDisabled(1);
            $form->getElement('frontend_input')->setDisabled(1);

            if (isset($disableAttributeFields[$model->getAttributeCode()])) {
                foreach ($disableAttributeFields[$model->getAttributeCode()] as $field) {
                    $form->getElement($field)->setDisabled(1);
                }
            }
        }
        $form->addValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'apply' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_apply')
        );
    }
}
