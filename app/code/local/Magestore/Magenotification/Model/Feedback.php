<?php

class Magestore_Magenotification_Model_Feedback extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('magenotification/feedback');
    }	
	
	public function updateData($data)
	{
		$is_updated = false;
		$dataObject = new Varien_Object($data);
		$feedbackDataObject = new Varien_Object($dataObject->getFeedback());
		$messages = $dataObject->getMessage();
		
		$helper = Mage::helper('magenotification');
		
		// update feedback
		if($feedbackDataObject->getStatus() != $this->getStatus()){
			$this->setStatus($feedbackDataObject->getStatus());
			$is_updated = true;
		}
		
		if($feedbackDataObject->getCouponCode() != $this->getCouponCode()){
			$this->setCouponCode($feedbackDataObject->getCouponCode());
			$is_updated = true;
		}
		
		if($feedbackDataObject->getCouponValue() != $this->getCouponValue()){
			$this->setCouponValue($feedbackDataObject->getCouponValue());
			$is_updated = true;
		}		
		
		if($feedbackDataObject->getExpiredCoupon() != $this->getExpiredCoupon()){
			$this->setExpiredCoupon($feedbackDataObject->getExpiredCoupon());
			$is_updated = true;
		}				
		
		$this->setUpdated(time());
		$this->save();
		
		//update message
		if(count($messages)){
			$messageModel = Mage::getModel('magenotification/feedbackmessage');
			foreach($messages as $message){
				$messageDataObject = new Varien_Object($message);
				$messageModel->import($messageDataObject);
			}
		}
		
		return $this;
	}
	
	public function getMessages()
	{
		return Mage::getResourceModel('magenotification/feedbackmessage_collection')
									->addFieldToFilter('feedback_code',$this->getCode())
									->setOrder('posted_time','DESC');
	}
}