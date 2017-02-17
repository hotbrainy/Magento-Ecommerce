<?php

class Magestore_Magenotification_Block_Adminhtml_Feedback_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('feedback_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('magenotification')->__('Feedback Detail'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('magenotification')->__('Feedback Detail'),
          'title'     => Mage::helper('magenotification')->__('Feedback Detail'),
          'content'   => $this->getLayout()->createBlock('magenotification/adminhtml_feedback_edit_tab_form')->toHtml(),
      ));
     
	if($this->getRequest()->getParam('id')){
		$this->addTab('message_section', array(
          'label'     => Mage::helper('magenotification')->__('Post Message'),
          'title'     => Mage::helper('magenotification')->__('Post Message'),
          'content'   => $this->getLayout()->createBlock('magenotification/adminhtml_feedback_edit_tab_message')->toHtml(),
		));	 
	  
		$this->addTab('history_section', array(
          'label'     => Mage::helper('magenotification')->__('View Posted Message'),
          'title'     => Mage::helper('magenotification')->__('View Posted Message'),
          'content'   => $this->getLayout()->createBlock('magenotification/adminhtml_feedback_edit_tab_history')->toHtml(),
		));	 	  
	}
      return parent::_beforeToHtml();
  }
}