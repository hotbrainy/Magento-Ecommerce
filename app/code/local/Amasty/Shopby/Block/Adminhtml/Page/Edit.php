<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
class Amasty_Shopby_Block_Adminhtml_Page_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id'; 
        $this->_blockGroup = 'amshopby';
        $this->_controller = 'adminhtml_page';
        
        $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('salesrule')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class' => 'save'
            ), 10);
       $this->_formScripts[] = " function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'continue/edit') } ";        
       $this->_formScripts[] = " function showOptions(sel) {
            if (!sel.value)
                return;
            new Ajax.Request('" . $this->getUrl('*/*/options', array('isAjax'=>true)) ."', {
                parameters: {code : sel.value, name: sel.id},
                onSuccess: function(transport) {
                    $('option_' + sel.id.substring(sel.id.length-1) ).up().update(transport.responseText);
                }
            });
        }";         
    }

    public function getHeaderText()
    {
        $header = Mage::helper('amshopby')->__('New Page');
        
        if (Mage::registry('amshopby_page')->getPageId()) {
            if (Mage::registry('amshopby_page')->getMetaTitle()) {
                $header = Mage::helper('amshopby')->__('Edit Page `%s`', Mage::registry('amshopby_page')->getMetaTitle());
            } else {
                $header = Mage::helper('amshopby')->__('Edit Page');
            }
        }
        return $header;         
    }
}