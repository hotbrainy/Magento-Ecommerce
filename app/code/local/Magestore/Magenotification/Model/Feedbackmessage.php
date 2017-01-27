<?php

class Magestore_Magenotification_Model_Feedbackmessage extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('magenotification/feedbackmessage');
    }	
	
	public function getAttachedFile()
	{
		$html = '';
		$attachedfiles = $this->getFile();
		if($attachedfiles){
			$attachedfiles = explode(',',$attachedfiles);
			$siteUrl = ((int)$this->getIsCustomer() == 1) ? Mage::getBaseUrl() : Magestore_Magenotification_Model_Keygen::SERVER_URL; 
			$siteUrl = str_replace('index.php/','',$siteUrl);
			if(count($attachedfiles)){
				$count = 1;
				foreach($attachedfiles as $attachedfile){
					$html .= '<br/>'.($count++).'. <a href="'.$siteUrl.'media/feedback'.$attachedfile.'">'.$this->getFileName($attachedfile).'</a>';
				}
			}
			$html .= '<br/><br/>';
		}
		return $html;
	}
	
	public function getFileName($path)
	{
		return substr($path,strrpos($path,'/')+1);
	}
	
	public function import($dataObject)
	{
		$message = $this->getCollection()
								->addFieldToFilter('feedback_code',$dataObject->getFeedbackCode())
								->addFieldToFilter('message',$dataObject->getMessage())
								->addFieldToFilter('file',$dataObject->getFile())
								->getFirstItem()
								;
		if(!$message->getId()){
			$dataObject->setFeedbackmessageId(null);
			$message->addData($dataObject->getData())
				->save();
		}
	}
}