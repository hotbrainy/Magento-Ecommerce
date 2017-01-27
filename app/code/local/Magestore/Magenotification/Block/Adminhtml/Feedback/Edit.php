<?php

class Magestore_Magenotification_Block_Adminhtml_Feedback_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'magenotification';
        $this->_controller = 'adminhtml_feedback';
        
        $this->_updateButton('save', 'label', Mage::helper('magenotification')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('magenotification')->__('Delete'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		
		if($this->getRequest()->getParam('id')){
			$this->_addButton('sendfeedback', array(
				'label'     => Mage::helper('adminhtml')->__('Resend'),
				'onclick'   => 'location.href=\''.$this->getUrl('*/*/resend',array('id'=>$this->getRequest()->getParam('id'))).'\'',
				'class'     => 'add',
			), -1);		
		}
		
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('magenotification_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'magenotification_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'magenotification_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('feedback_data') && Mage::registry('feedback_data')->getId() ) {
            return Mage::helper('magenotification')->__("Edit Feedback for '%s'", $this->htmlEscape(Mage::registry('feedback_data')->getExtension()));
        }
    }
}