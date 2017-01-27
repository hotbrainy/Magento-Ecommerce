<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

class Amasty_Shopby_Block_Adminhtml_Filter_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'filer_id';
        $this->_blockGroup = 'amshopby';
        $this->_controller = 'adminhtml_filter';

        parent::__construct();
        $this->_removeButton('reset');

        $this->_addButton('save_and_continue', array(
            'label'     => Mage::helper('amshopby')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class' => 'save'
        ), 10);
        $this->_formScripts[] = "function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'continue/edit') }";
    }

    public function getHeaderText()
    {
        $model = Mage::registry('amshopby_filter');

        if ($model) {
            $attribute =  Mage::getModel('eav/entity_attribute')->load($model->getAttributeId());
            return Mage::helper('amshopby')->__('Edit Filter "' . $attribute->getFrontendLabel() . '" Properties');
        }
    }
}
