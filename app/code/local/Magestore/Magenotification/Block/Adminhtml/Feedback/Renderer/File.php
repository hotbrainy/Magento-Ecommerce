<?php 
class Magestore_Magenotification_Block_Adminhtml_Feedback_Renderer_File
	extends Mage_Core_Block_Template
{
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$this->setTemplate('magenotification/feedback/files.phtml');
		return $this;
	}
	
	public function getAttachedFilesHtml()
	{
		$feedback = $this->getFeedback();
		$html = '';
		$attachedfiles = $feedback->getFile();
		if($attachedfiles){
			$attachedfiles = explode(',',$attachedfiles);
			if(count($attachedfiles)){
				$count = 1;
				foreach($attachedfiles as $attachedfile){
					$html .= '<br/>'.($count++).'. <a href="'.Mage::getBaseUrl('media').'feedback'.$attachedfile.'">'.$this->getFileName($attachedfile).'</a>';
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
}