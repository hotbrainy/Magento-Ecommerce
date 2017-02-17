<?php

class Magestore_Magenotification_Adminhtml_MagenotificationController extends Mage_Adminhtml_Controller_Action
{

	public function readdetailAction()
	{
		$id = $this->getRequest()->getParam('id');
		$notice = Mage::getModel('adminnotification/inbox')->load($id);
		$notice->setIsRead(1);
		$notice->save();
		return $this->_redirectUrl($notice->getUrl());
	}

}