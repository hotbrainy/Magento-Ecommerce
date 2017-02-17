<?php

class Magestore_Magenotification_Block_Adminhtml_Feedback_Edit_Tab_History 
	extends Mage_Adminhtml_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('magenotification/feedback/history.phtml');
	}
	
	public function getFeedback()
	{
		return Mage::registry('feedback_data');
	}
	
	public function getMessages()
	{
		return $this->getFeedback()->getMessages();
	}
	
	public function getMessageTitle($message)
	{
		$title = '<b>';
		$title .= ($message->getIsCustomer() == '1') ? $this->__('From Admin') : $this->__('From').' '.Magestore_Magenotification_Model_Keygen::SERVER_NAME; 
		$title .= ' - '.$message->getUser().'</b> ';
		$title .= $this->__('on').' ';
		$title .= Mage::helper('core')->formatDate($message->getPostedTime(),'medium',true);	
		return $title;
	}
}