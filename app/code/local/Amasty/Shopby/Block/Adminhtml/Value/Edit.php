<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

class Amasty_Shopby_Block_Adminhtml_Value_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'amshopby';
        $this->_controller = 'adminhtml_value';

        $this->_removeButton('reset');
        $this->_removeButton('delete');

        $this->_addButton('save_and_continue', array(
            'label'     => Mage::helper('amshopby')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class' => 'save'
        ), 10);
        $this->_formScripts[] = "function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'continue/edit') }";

        $this->_formScripts[] = " function featured(sel) {

            if (sel.value ==  1) {
                sel.up('tr').next('tr').show();
            } else {
                sel.up('tr').next('tr').hide();
            }

        }featured($('is_featured'));";
    }

    public function getHeaderText()
    {
        return Mage::helper('amshopby')->__('Option Properties');
    }

    public function getBackUrl()
    {
        /** @var Amasty_Shopby_Model_Value $value */
        $value = Mage::registry('amshopby_value');
        return $this->getUrl('adminhtml/amshopby_filter/edit', array('id' => $value->getFilterId()));
    }
}