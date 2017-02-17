<?php

class Magestore_Magenotification_Helper_Data extends Mage_Core_Helper_Abstract
{
	
	const LICENSE_INFO_PATH = 'magenotificationsecure/extension_keys/Magestore_{{extension_name}}_license_info';
	//const LICENSE_INFO_FORMAT1 = '<hr width="280" /><b>{{license_type}}</b><br/>{{activated_time}}<br/>{{domains}} {{expired_info}}<hr width="280" />';
	const LICENSE_INFO_FORMAT1 = '<hr width="345" /><b>{{license_type}}</b><br/>{{activated_time}}<br/>{{domains}} {{expired_info}}<hr width="345" />';
	const LICENSE_INFO_FORMAT2 = "<hr/><pre> {{license_type}} \n {{activated_time}} \n {{domains}} {{expired_info}}</pre><hr/>";
	
	public $PASSED_KEYWORDS = array('magestore','localhost','test.','test/','dev/','dev.','development','demo-');
	
	protected $_extension;
	protected $_licenseType = array();
	protected $_errorMessage = null;
	protected $_logger = array();
    
    public function checkLicenseKeyFrontController($controller) {return true;}
    public function checkLicenseKeyAdminController($controller) {return true;}
    public function checkLicenseKey($extensionName) {return true;}
	
	// used for checking license in front-end controller
	public function checkTrialKeyFrontController($controller)
	{
		$extensionName = get_class($controller);
		if(!$this->checkTrialKey($extensionName)){
			$request = $controller->getRequest();
			$request->initForward();	
			$request->setActionName('noRoute')->setDispatched(false);				
			return false;
		} else {
			return true;
		}
	}
	
	// used for checking license in back-end controller
	public function checkTrialKeyAdminController($controller)
	{	
		$extensionName = get_class($controller);
		if(!$this->checkTrialKey($extensionName)){
			$message = $this->getInvalidKeyNotice();
			$controller->loadLayout();
			$contentBlock = $controller->getLayout()->createBlock('core/text');
			$contentBlock->setText($message);
			$controller->getLayout()->getBlock('root')->setChild('content',$contentBlock);
			$controller->renderLayout();
			return false;
		}elseif((int)$this->getDBLicenseType() == Magestore_Magenotification_Model_Keygen::TRIAL_VERSION
			|| (int)$this->getDBLicenseType() == Magestore_Magenotification_Model_Keygen::DEVELOPMENT){
			$versionLabel = (int)$this->getDBLicenseType() == Magestore_Magenotification_Model_Keygen::TRIAL_VERSION ? $this->__('trial') : $this->__('development');
			Mage::getSingleton('core/session')->addNotice($this->__('You are using a %s version of %s extension. It will be expired on %s.',
					$versionLabel,
					$this->_extension,
					$this->getDBExpiredTime()
				));
		}
		return true;	
	}	
	
	public function checkTrialKey($extensionName)
	{
		if(strpos('a'.$extensionName,'Magestore')){
			$arrName = explode('_',$extensionName);
			$extensionName = isset($arrName[1]) ? $arrName[1] : str_replace('Magestore','',$extensionName);
		}
		$this->_extension = $extensionName;
		//$baseUrl = Mage::getBaseUrl();
		$baseUrl = Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID)->getBaseUrl();
			//check passed key words
		foreach($this->PASSED_KEYWORDS as $passed_keyword){
			if(strpos($baseUrl,$passed_keyword))
				return true;	
		}
			
		$domain = $this->getDomain($baseUrl);
        
        // Check Depend Extension, Use Key of Major Extension
        $config = Mage::getSingleton('magenotification/config');
        $moduleConfig = $config->getNode('Magestore_'.$extensionName);
        if ($moduleConfig !== false
            && (string)$moduleConfig->type == Magestore_Magenotification_Model_Config::DEPEND_LICENSE
            && (string)$moduleConfig->depend
        ) {
            $extensionName = (string)$moduleConfig->depend;
            $arrName = explode('_',$extensionName);
            $extensionName = isset($arrName[1]) ? $arrName[1] : str_replace('Magestore','',$extensionName);
            $this->_extension = $extensionName;
        }
        
		$licensekey = Mage::getStoreConfig('magenotificationsecure/extension_keys/Magestore_'.$extensionName);
		$licensekey = trim($licensekey);
	
		//get cached data
		if($this->getDBLicenseKey() == $licensekey
			&& $this->getDBCheckdate() == date('Y-m-d')
			&& $this->getDBSumcode() == $this->getSumcode()){
			$responsecode = $this->getDBResponseCode();
		} else {
		//check license key online
			$responsecode = Mage::getSingleton('magenotification/keygen')->checkLicenseKey($licensekey,$extensionName,$domain);
		//save data into database
			$this->setDBLicenseKey($licensekey);
			$this->setDBCheckdate(date('Y-m-d'));
			$this->setDBResponseCode((int)$responsecode);	
			$this->setDBSumcode($this->getSumcode($responsecode));	
			$this->_saveLicenseLog();
		}
        
        // Check Packaged Trial License Key
        if (!$licensekey || $responsecode < Magestore_Magenotification_Model_Keygen::NEW_DOMAIN_SUCCESS) {
            $moduleConfig = $config->getNode('Magestore_'.$extensionName);
            if ($moduleConfig !== false
                && (string)$moduleConfig->type == Magestore_Magenotification_Model_Config::TRIAL_LICENSE
                && (string)$moduleConfig->trial_key
            ) {
                $licensekey = trim((string)$moduleConfig->trial_key);
                
                //get cached data
                if($this->getDBLicenseKey() == $licensekey
                    && $this->getDBCheckdate() == date('Y-m-d')
                    && $this->getDBSumcode() == $this->getSumcode()){
                    $responsecode = $this->getDBResponseCode();
                } else {
                //check license key online
                    $responsecode = Mage::getSingleton('magenotification/keygen')->checkLicenseKey($licensekey,$extensionName,$domain);
                //save data into database
                    $this->setDBLicenseKey($licensekey);
                    $this->setDBCheckdate(date('Y-m-d'));
                    $this->setDBResponseCode((int)$responsecode);	
                    $this->setDBSumcode($this->getSumcode($responsecode));	
                    $this->_saveLicenseLog();
                }
            }
        }
        
		//save error message
		$this->_errorMessage = $this->getLicenseKeyError($responsecode);
		return $this->isValidCode($responsecode);
	}
	
	public function getErrorMessage()
	{
		return $this->_errorMessage;
	}
	
	public function isValidCode($code)
	{
		if(in_array((int)$code,array(Magestore_Magenotification_Model_Keygen::NEW_DOMAIN_SUCCESS,
								     Magestore_Magenotification_Model_Keygen::EXISTED_DOMAIN_SUCCESS,
								     Magestore_Magenotification_Model_Keygen::SUB_DOMAIN_SUCCESS,
		))){
			return true;
		}
		return false;
	}
	
	public function getLicenseInfo($licensekey,$extensionName)
	{
		if(strpos('a'.$extensionName,'Magestore')){
			$arrName = explode('_',$extensionName);
			$extensionName = isset($arrName[1]) ? $arrName[1] : str_replace('Magestore','',$extensionName);
		}
		$this->_extension = $extensionName;	
		$configPath = str_replace('{{extension_name}}',$extensionName,self::LICENSE_INFO_PATH);
		//$baseUrl = Mage::getBaseUrl();
		$baseUrl = Mage::getModel('core/store')->load(Mage_Core_Model_App::ADMIN_STORE_ID)->getBaseUrl();
		$domain = $this->getDomain($baseUrl);
		
		//get cached data
		if($this->getDBLicenseKey() == $licensekey
			&& $this->getDBCheckdate() == date('Y-m-d')
			&& $this->getDBSumcode() == $this->getSumcode()){
			//return Mage::getStoreConfig($configPath);
		}
		
		$licenseinfo = Mage::getSingleton('magenotification/keygen')->getLicenseInfo($licensekey,$extensionName,$domain);
		$responsecode = $licenseinfo->getResponseCode();
		$licenseType = $licenseinfo->getType();
		if((int)$licenseinfo->getResponseCode() == 101){ // error when get license info
			return Mage::getStoreConfig($configPath);
		}
		
		if((int)$licenseinfo->getResponseCode() >= Magestore_Magenotification_Model_Keygen::NEW_DOMAIN_SUCCESS){
			//save license type
			$this->_licenseType[$extensionName] = $licenseinfo->getType();
			//get license info html format
			$licenseinfo = $this->fomatLicenseInfo($licenseinfo);	
			$response = 1;
		} else{
			//get license info
			$message = $this->getLicenseKeyError((int)$licenseinfo->getResponseCode());
			$showLicenseinfo = null;
			if(in_array((int)$licenseinfo->getResponseCode(),array( Magestore_Magenotification_Model_Keygen::EXPIRED_TRIAL_LICENSE_KEY_ERROR,
																	Magestore_Magenotification_Model_Keygen::LIMITED_DOMAIN_ERROR,
						))){
				$showLicenseinfo = $this->fomatLicenseInfo($licenseinfo);	
			}
			//save license type
			$this->_licenseType[$extensionName] = -1;	
			if((int)$licenseinfo->getResponseCode() == Magestore_Magenotification_Model_Keygen::LIMITED_DOMAIN_ERROR){
				$this->_licenseType[$extensionName] = $licenseinfo->getType();	
			}
			$licenseinfo = '<hr width="345" /><span class="licensekey-warning" style="font-weight:bold;color:#FF0000;">'.$message.'</span><br/>'.$showLicenseinfo.'<hr width="345" />';
			$reponse = 0;
		}
		//save data into cookie
		$this->setDBLicenseKey($licensekey);
		$this->setDBLicenseType($licenseType);
		$this->setDBCheckdate(date('Y-m-d'));
		$this->setDBResponseCode((int)$responsecode);	
		$this->setDBSumcode($this->getSumcode($responsecode));	
		$this->_saveLicenseLog();
		Mage::getSingleton('core/config')->saveConfig($configPath,$licenseinfo);
		
		return $licenseinfo;			
	}
	
	public function getLicenseType($extension)
	{
		$extension = $this->getExtensionName($extension);
		if(!isset($this->_licenseType[$extension]))
			return 0;
		return $this->_licenseType[$extension];
	}
	
	public function fomatLicenseInfo($info)
	{
		$helper = Mage::helper('magenotification');
		$html = self::LICENSE_INFO_FORMAT1;
		$licensetype = Magestore_Magenotification_Model_Keygen::getLicenseTitle((int)$info->getType());
		$activetime = Mage::helper('core')->formatDate($info->getActivatedTime(),'medium',false);
		$domains = $info->getDomains();
		$expired_info = null;
		if((int)$info->getType() == Magestore_Magenotification_Model_Keygen::TRIAL_VERSION
			|| (int)$info->getType() == Magestore_Magenotification_Model_Keygen::DEVELOPMENT){
			$expired_time = strtotime($info->getActivatedTime()) + (int) $info->getExpiredTime() *3600*24;
			$expired_time = date('Y-m-d H:i:s',$expired_time);
			$expired_info = '<b>'.$helper->__('Expired Date').':</b> '.Mage::helper('core')->formatDate($expired_time,'medium',false);
			$expired_info = " <br/> ".$expired_info;
			$this->setDBExpiredTime(Mage::helper('core')->formatDate($expired_time,'medium',false));
		}
		
		$html = str_replace('{{license_type}}',$licensetype,$html);
		$html = str_replace('{{activated_time}}','<b>'.$helper->__('Active Date').':</b> '.$activetime,$html);
		$html = str_replace('{{domains}}','<b>'.$helper->__('Domain').':</b> '.$info->getDomains(),$html);
		$html = str_replace('{{expired_info}}',$expired_info,$html);
		return $html;
	}
	
	public function getSumcode($reponse=null)
	{
		$reponse = $reponse ? $reponse : $this->getDBResponseCode();
		return md5(date('Y-m-d').'$295@-magestore_checklicensekey'.$reponse);
	}
	
	/***********/
	/* Use cookie for storeing license info */
	/***********/	
	
	public function setCookieData($key,$value)
	{
		$cookie = Mage::getSingleton('core/cookie');
		$cookie->set($key.'_'.$this->_extension,$value);	
		return $this;	
	}
	
	public function getCookieData($key)
	{
		$cookie = Mage::getSingleton('core/cookie');
		return $cookie->get($key.'_'.$this->_extension);	
	}			
	
	public function setCookieLicenseType($licenseType)
	{
		$cookie = Mage::getSingleton('core/cookie');
		$cookie->set('licensetype_'.$this->_extension,$licenseType);	
		return $this;
	}
	
	public function getCookieLicenseType()
	{
		$cookie = Mage::getSingleton('core/cookie');
		return $cookie->get('licensetype_'.$this->_extension);		
	}	
	
	public function getCookieLicenseKey()
	{
		$cookie = Mage::getSingleton('core/cookie');
		return $cookie->get('licensekey_'.$this->_extension);
	}
	
	public function setCookieLicenseKey($key)
	{
		$cookie = Mage::getSingleton('core/cookie');
		return $cookie->set('licensekey_'.$this->_extension,$key);
	}	

	public function getCookieCheckdate()
	{
		$cookie = Mage::getSingleton('core/cookie');
		return $cookie->get('checkdate_'.$this->_extension);	
	}
	
	public function setCookieCheckdate($date)
	{
		$cookie = Mage::getSingleton('core/cookie');
		return $cookie->set('checkdate_'.$this->_extension,$date);	
	}	
	
	public function getCookieSumcode()
	{
		$cookie = Mage::getSingleton('core/cookie');
		return $cookie->get('sumcode_'.$this->_extension);	
	}	
	
	public function setCookieSumcode($sumcode)
	{
		$cookie = Mage::getSingleton('core/cookie');
		return $cookie->set('sumcode_'.$this->_extension,$sumcode);	
	}	

	public function getCookieResponseCode()
	{
		$cookie = Mage::getSingleton('core/cookie');
		return $cookie->get('reponsecode_'.$this->_extension);	
	}	
	
	public function setCookieResponseCode($reponseCode)
	{
		$cookie = Mage::getSingleton('core/cookie');
		return $cookie->set('reponsecode_'.$this->_extension,$reponseCode);	
	}		
	
	public function getCookieIsvalid()
	{
		$cookie = Mage::getSingleton('core/cookie');
		return $cookie->get('valid_'.$this->_extension);	
	}	
	
	public function setCookieIsvalid($isvalid)
	{
		$cookie = Mage::getSingleton('core/cookie');
		return $cookie->set('valid_'.$this->_extension,$isvalid);	
	}	
	
	/* End of using cookie for storeing license info */

	
	/***********/
	/*Use database for storeing license info*/
	/***********/
	
	public function setDBLicenseType($licenseType)
	{
		$this->_getLogger()->setData('license_type',$licenseType);
	}
	
	public function getDBLicenseType()
	{
		return $this->_getLogger()->getData('license_type');
	}	
	
	public function setDBLicenseKey($key)
	{
		$this->_getLogger()->setData('license_key',$key);
	}	
	
	public function getDBLicenseKey()
	{
		 return $this->_getLogger()->getData('license_key');
	}		
	
	public function setDBCheckdate($checkdate)
	{
		$this->_getLogger()->setData('check_date',$checkdate);
	}			
	
	public function getDBCheckdate()
	{
		return $this->_getLogger()->getData('check_date');
	}		
	
	public function setDBSumCode($sumcode)
	{
		$this->_getLogger()->setData('sum_code',$sumcode);
	}			
	
	public function getDBSumCode()
	{
		return $this->_getLogger()->getData('sum_code');
	}		
	
	public function setDBResponseCode($response_code)
	{
		$this->_getLogger()->setData('response_code',$response_code);
	}			
	
	public function getDBResponseCode()
	{
		return $this->_getLogger()->getData('response_code');
	}	
	
	public function setDBIsvalid($is_valid)
	{
		$this->_getLogger()->setData('is_valid',$is_valid);
	}			
	
	public function getDBIsvalid()
	{
		return $this->_getLogger()->getData('is_valid');
	}	
	
	public function setDBExpiredTime($expired_time)
	{
		$this->_getLogger()->setData('expired_time',$expired_time);
	}			
	
	public function getDBExpiredTime()
	{
		return $this->_getLogger()->getData('expired_time');
	}		
	
	protected function _getLogger()
	{
		//if($this->_logger == null){
		if (!isset($this->_logger[$this->_extension])){
			$this->_logger[$this->_extension] = Mage::getResourceModel('magenotification/logger_collection')
										->addFieldToFilter('extension_code',$this->_extension)
										->getFirstItem();
			$this->_logger[$this->_extension]->setData('extension_code',$this->_extension);
		}
		return $this->_logger[$this->_extension];
	}
	
	protected function _saveLicenseLog()
	{
		if($this->_extension != null){
			$this->_getLogger()->save();
		}
	}
	
	/* End of using database for storeing license info */
	
	public function getDomain($baseUrl){
		$parseUrl = parse_url(trim($baseUrl));
		$domain = trim($parseUrl['host'] ? $parseUrl['host'] : array_shift(explode('/', $parseUrl['path'], 2)));
		if (strpos($domain,'www.') === 0) $domain = substr($domain,4);
		return $domain;
	}
	
	public function getExtensionList()
	{
		$list = array();
        $modules = (array) Mage::getConfig()->getNode('modules')->children();
		
        foreach ($modules as $moduleName => $moduleInfo) {
            if ($moduleName==='Magestore_Magenotification') {
                continue;
            }			
			if(strpos('a'.$moduleName,'Magestore') == 0){
				continue;
			}
			if((string)$moduleInfo->codePool != 'local'){
				continue;
			}
			$moduleName = str_replace('Magestore_','',$moduleName);
			$list[$moduleName] = $moduleName;
		}
		return $list;
	}
	
	public function getExtensionOption()
	{
		$options = array();
		$list = $this->getExtensionList();
		if(count($list)){
			foreach($list as $key=>$item){
				$options[] = array('value'=>$key,'label'=>$item);
			}
		}
		return $options;	
	}
	
	public function getFeedbackStatusList()
	{
		$list = array();
		$list[1] = $this->__('Approved');
		$list[2] = $this->__('Canceled');
		$list[3] = $this->__('Pending');
		return $list;
	}
	
	public function getFeedbackStatusOption()
	{
		$options = array();
		$list = $this->getFeedbackStatusList();
		if(count($list)){
			foreach($list as $key=>$item){
				$options[] = array('value'=>$key,'label'=>$item);
			}
		}
		return $options;	
	}		
	
	public function getExtensionVersion($extension)
	{
        $modules = Mage::getConfig()->getNode('modules')->children();
		$modules = (array) $modules;
		if(isset($modules[$extension])){
			return (string)$modules[$extension]->version;	
		}
		if(isset($modules['Magestore_'.$extension])){
			return (string)$modules['Magestore_'.$extension]->version;	
		}
		return null;
	}
	
	public function getExtensionName($extension)
	{
		if(strpos('a'.$extension,'Magestore')){
			$arrName = explode('_',$extension);
			$extension = isset($arrName[1]) ? $arrName[1] : str_replace('Magestore','',$extension);
		}	
		return $extension;	
	}
	
	public function getInvalidKeyNotice()
	{
		$extensionkey_link = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/system_config/edit',array('section'=>'magenotificationsecure','_secure'=>true));
		$extensionkey_link = '<a href="'.$extensionkey_link.'">'.$this->__('extension key management section').'</a>';
		$message = $this->getErrorMessage();
		$message = $message ? $message : $this->__('Invalid License Key');
		$message .= '! '.$this->__('Please go to %s for more information.',$extensionkey_link);
		$message = '<div id="messages"><ul class="messages"><li class="error-msg"><ul><li>'.$message.'</li></ul></li></ul></div>';
		return $message;
	}
	
	public function getLicenseKeyError($error_code)
	{
		switch($error_code){
			case Magestore_Magenotification_Model_Keygen::GENERAL_ERROR :
				return $this->__('Invalid License Key');
			case Magestore_Magenotification_Model_Keygen::NOT_EXIST_LICENSE_KEY_ERROR :
				return $this->__('Invalid License Key');
			case Magestore_Magenotification_Model_Keygen::DISABLED_LICENSE_KEY_ERROR :
				return $this->__('Invalid License Key');
			case Magestore_Magenotification_Model_Keygen::EXPIRED_TRIAL_LICENSE_KEY_ERROR :
				return $this->__('Expired License');
			case Magestore_Magenotification_Model_Keygen::LIMITED_DOMAIN_ERROR :
				return $this->__('License key does not match');
		}
		return null;
	}

}