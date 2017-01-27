<?php

class Magestore_Magenotification_Block_Adminhtml_Feedback_Edit_Tab_Message extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('feedback_message', array('legend'=>Mage::helper('magenotification')->__('Post Message')));	  
	  
		$fieldset->addField('message', 'editor', array(
          'name'      => 'message',
          'label'     => Mage::helper('magenotification')->__('Message'),
		  'style'     => 'width:600px;height:150px',
          'class'     => 'required-entry',
          'required'  => true,				  
		));			
		
		$fieldset->addField('attached_file', 'note', array(
          'name'      => 'attached_file',
          'label'     => Mage::helper('magenotification')->__('Attached Files'),
          'text'      => $this->getLayout()->createBlock('magenotification/adminhtml_feedback_renderer_file')->toHtml(),			  
		));	
		

      return parent::_prepareForm();
  }
}