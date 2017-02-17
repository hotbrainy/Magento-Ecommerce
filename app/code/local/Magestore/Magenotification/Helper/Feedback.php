<?php

class Magestore_Magenotification_Helper_Feedback extends Mage_Core_Helper_Abstract
{
	public function getResumeCode($feedbackcode)
	{
		return md5($feedbackcode.'magestore-extension-feedback-59*47@');
	}
	
	public function postFeedback($feedback)
	{
		$baseUrl = Mage::getBaseUrl();
		$resume = $this->getResumeCode($feedback->getCode());
		$customeremail = Mage::getStoreConfig('magestore_extension_feedback/email/contactemail');
		$customername = Mage::getStoreConfig('magestore_extension_feedback/email/contactname');
		if(!$customeremail){
			$customeremail = Mage::getStoreConfig('trans_email/ident_general/email');
			$customername = Mage::getStoreConfig('trans_email/ident_general/name');
		}
		/* try{
			$xmlRpc = new Zend_XmlRpc_Client(Magestore_Magenotification_Model_Keygen::SERVER_URL.'api/xmlrpc/');
			$session = $xmlRpc->call('login', array('username'=>Magestore_Magenotification_Model_Keygen::WEBSERVICE_USER,'password'=>Magestore_Magenotification_Model_Keygen::WEBSERVICE_PASS));
			$result = $xmlRpc->call('call', array('sessionId' => $session,
												  'apiPath'   => 'licensemanager_extensionfeedback.feedbackupdate',
												  'args'      => array( $baseUrl,
																		$feedback->getCode(),
																		$feedback->getExtension(),
																		$feedback->getExtensionVersion(),
																		$feedback->getContent(),
																		$feedback->getFile(),
																		$feedback->getMessage(),
																		$customeremail,
																		$customername,
																		$resume,
								)));
			if((int)$result < 210){ //error
				throw new Exception($this->__('Can not sent feedback! Please try later').$result);
			}
		} catch(Exception $e){
			throw new Exception($this->__('Can not sent feedback! Please try later').'<br/>'.$e->getMessage());
		}	 */		
	}
	
	public function postMessage($message)
	{
		$resume = $this->getResumeCode($message->getFeedbackCode());
		$customeremail = Mage::getStoreConfig('magestore_extension_feedback/email/contactemail');
		$customername = Mage::getStoreConfig('magestore_extension_feedback/email/contactname');
		if(!$customeremail){
			$customeremail = Mage::getStoreConfig('trans_email/ident_general/email');
			$customername = Mage::getStoreConfig('trans_email/ident_general/name');
		}		
		/* try{
			$xmlRpc = new Zend_XmlRpc_Client(Magestore_Magenotification_Model_Keygen::SERVER_URL.'api/xmlrpc/');
			$session = $xmlRpc->call('login', array('username'=>Magestore_Magenotification_Model_Keygen::WEBSERVICE_USER,'password'=>Magestore_Magenotification_Model_Keygen::WEBSERVICE_PASS));
			$result = $xmlRpc->call('call', array('sessionId' => $session,
												  'apiPath'   => 'licensemanager_extensionfeedback.addmessage',
												  'args'      => array( $message->getFeedbackCode(),
																		$customername,
																		$message->getMessage(),
																		$message->getFile(),
																		$message->getPostedTime(),
																		$resume,
								)));
			if((int)$result < 210){ //error
				throw new Exception($this->__('Can not sent message! Please try later').$result);
			}
		} catch(Exception $e){
			throw new Exception($this->__('Can not sent message! Please try later').'<br/>'.$e->getMessage());
		}	 */		
	}
	
	public function updateFeedback($feedback)
	{
		$resume = $this->getResumeCode($feedback->getCode());
		/* try{
			$xmlRpc = new Zend_XmlRpc_Client(Magestore_Magenotification_Model_Keygen::SERVER_URL.'api/xmlrpc/');
			$session = $xmlRpc->call('login', array('username'=>Magestore_Magenotification_Model_Keygen::WEBSERVICE_USER,'password'=>Magestore_Magenotification_Model_Keygen::WEBSERVICE_PASS));
			$result = $xmlRpc->call('call', array('sessionId' => $session,
												  'apiPath'   => 'licensemanager_extensionfeedback.feedbackinfo',
												  'args'      =>  array($feedback->getCode(),
																		$resume,
										)));
			$feedback->updateData($result);
		} catch(Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
		}	 */		
	}
	
	public function needUpdate($feedback)
	{	
		if(!$feedback->getId())
			return false;
		$updated = $feedback->getUpdated();
		if((int)$updated + 60 < time()){
			return true;
		}
		return false;
	}
	
	public function getSentStatusList()
	{
		$list = array();
		$list[1] = $this->__('Sent');
		$list[2] = $this->__('Not Sent');
		return $list;
	}
	
	public function getSentStatusOption()
	{
		$options = array();
		$list = $this->getSentStatusList();
		if(count($list)){
			foreach($list as $key=>$item){
				$options[] = array('value'=>$key,'label'=>$item);
			}
		}
		return $options;	
	}		
}