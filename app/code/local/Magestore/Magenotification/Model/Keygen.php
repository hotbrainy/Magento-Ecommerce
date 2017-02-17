<?php

class Magestore_Magenotification_Model_Keygen
{
	const DOMAIN1 = 1;
	const DOMAIN2 = 2;
	const DOMAIN5 = 3;
	const DOMAIN10 = 4;
	const UNLIMITED = 5;
	const DEVELOPER = 6;
	const DEVELOPMENT = 7;
	const TRIAL_VERSION = 10;
	
	const GENERAL_ERROR = 200;
	const NOT_EXIST_LICENSE_KEY_ERROR = 201;
	const DISABLED_LICENSE_KEY_ERROR = 202;
	const EXPIRED_TRIAL_LICENSE_KEY_ERROR = 203;
	const LIMITED_DOMAIN_ERROR = 204;
	const NEW_DOMAIN_SUCCESS = 210;
	const EXISTED_DOMAIN_SUCCESS = 211;
	const SUB_DOMAIN_SUCCESS = 212;	
	
	const SERVER_NAME = 'MageStore.com';
	const SERVER_URL = 'http://www.magestore.com/';
	const WEBSERVICE_USER = 'license_checker';
	const WEBSERVICE_PASS = 'Ki97@M0$l!';
	
	private $_publicKey = '-----BEGIN PUBLIC KEY-----
MFwwDQYJKoZIhvcNAQEBBQADSwAwSAJBAJ8EDi+a0lilUChsDba33FrcHLZZZIMx
T7XhyEP3J3llQXNJkflG+5GzBvFTd+B1pvpc45WOktNReyPDZ/OMNukCAwEAAQ==
-----END PUBLIC KEY-----';
	
    public function checkLicenseKey($licensekey,$extensionName,$domain){
		if ($licenseInfo = $this->getLicenseInfo($licensekey,$extensionName,$domain)){
			if ($licenseInfo->getResponseCode() == 101) return self::NEW_DOMAIN_SUCCESS;
			return $licenseInfo->getResponseCode();
		}
		return self::GENERAL_ERROR;
	}
	
	public function getLicenseInfo($licensekey,$extensionName,$domain){
		$licensekey = trim($licensekey);
		// check license for old license key
		if ($oldLicenseInfo = $this->_decrypt($licensekey)){
			$license = Mage::getModel('magenotification/license')->loadByLicenseExtension($licensekey,$extensionName);
			if (!$license->getId()){
				// Request first time when using old license key
				try {
					$xmlRpc = new Zend_XmlRpc_Client(self::SERVER_URL.'api/xmlrpc/');
					$session = $xmlRpc->call('login', array('username' => self::WEBSERVICE_USER, 
													'password' => self::WEBSERVICE_PASS));
					$result = $xmlRpc->call('call', array('sessionId' => $session,
												  'apiPath'   => 'licensemanager.active',
												  'args'      => array($licensekey,$extensionName,$domain)));
					$dataObject = new Varien_Object($result);
				} catch (Exception $e){
					return new Varien_Object(array('response_code' => 101));
				}
				$license->setResponseCode($dataObject->getResponseCode())
					->setDomains($dataObject->getDomains());
				if ($dataObject->getActivatedTime()){
					$license->setActiveAt(substr($dataObject->getActivatedTime(),0,10));
				} else {
					$license->setActiveAt(now(true));
				}
				try {
					$license->setSumCode($this->_getSumCode($license));
					$license->save();
				} catch(Exception $e) {}
				return $dataObject;
			}
			$result = new Varien_Object($license->getData());
			$result->addData(array(
				'users'		=> 1,
				'created_time'	=> date('Y-m-d H:m:s',$oldLicenseInfo['created_time']),
				'activated_time'	=> $license->getActiveAt(),
				'type'		=> $oldLicenseInfo['type'],
				'status'	=> 1,
				'expired_time'	=> $oldLicenseInfo['expired_time'],
			));
			if ($license->getSumCode() != $this->_getSumCode($license)){
				try {
					$license->setResponseCode(self::GENERAL_ERROR);
					$license->setSumCode($this->_getSumCode($license))->save();
				} catch (Exception $e){}
			} elseif ($result->getType() == self::TRIAL_VERSION && $license->getResponseCode() > self::LIMITED_DOMAIN_ERROR){
				$expiredTime = strtotime($license->getActiveAt()) + (int)$result->getExpiredTime() * 24*3600;
				if ($expiredTime < time()){
					try {
						$license->setResponseCode(self::EXPIRED_TRIAL_LICENSE_KEY_ERROR);
						$license->setSumCode($this->_getSumCode($license))->save();
					} catch (Exception $e){}
				}
			}
			return $result->setResponseCode($license->getResponseCode());
		}
		// check new license key
		if ($licenseInfo = $this->_newkeyDecrypt($licensekey,$extensionName,$domain)){
			$license = Mage::getModel('magenotification/license')->loadByLicenseExtension($licensekey,$extensionName);
			if (!$license->getId()){
				$license->setActiveAt(now(true))
					->setDomains($licenseInfo->getDomains());
				$responseCode = self::NEW_DOMAIN_SUCCESS;
				$licenseDomain = (strlen($domain) > 38) ? substr($domain,0,38) : $domain;
				if ($licenseDomain != $licenseInfo->getDomains())
					$responseCode = self::LIMITED_DOMAIN_ERROR;
				try {
					$license->setResponseCode($responseCode);
					$license->setSumCode($this->_getSumCode($license))->save();
				} catch (Exception $e){}
			}
			$result = new Varien_Object($license->getData());
			$createdTime = $licenseInfo->getCreatedDate() ? $licenseInfo->getCreatedDate() : $license->getActiveAt();
			$result->addData(array(
				'users'		=> 1,
				'created_time'	=> date('Y-m-d H:m:s',strtotime($createdTime)),
				'activated_time'	=> $license->getActiveAt(),
				'type'		=> $this->getOldLicenseType($licenseInfo->getType()),
				'status'	=> 1,
				'expired_time'	=> $licenseInfo->getExpiredTime(),
			));
			if ($license->getSumCode() != $this->_getSumCode($license)){
				try {
					$license->setResponseCode(self::GENERAL_ERROR);
					$license->setSumCode($this->_getSumCode($license))->save();
				} catch (Exception $e){}
			} elseif (($result->getType() == self::TRIAL_VERSION || $result->getType() == self::DEVELOPMENT)
				&& $license->getResponseCode() > self::LIMITED_DOMAIN_ERROR){
				$expiredTime = strtotime($license->getActiveAt()) + (int)$result->getExpiredTime() * 24*3600;
				if ($expiredTime < time()){
					try {
						$license->setResponseCode(self::EXPIRED_TRIAL_LICENSE_KEY_ERROR);
						$license->setSumCode($this->_getSumCode($license))->save();
					} catch (Exception $e){}
				}
			}
			return $result->setResponseCode($license->getResponseCode());
		}
		return new Varien_Object(array('response_code' => self::GENERAL_ERROR));
	}
	
	public function getOldLicenseType($newType){
		if ($newType == 'C')
			return self::DOMAIN1;
		if ($newType == 'D')
			return self::DEVELOPMENT;
		return self::TRIAL_VERSION;
	}
	
	private function _newkeyDecrypt($licensekey,$extensionName,$domain){
		if (strlen($licensekey) < 68) return false;
		$crc32Pos = abs(crc32(substr($licensekey,0,10).$extensionName) & 0x7FFFFFFF % 49) + 10;
		$crc32 = substr($licensekey,$crc32Pos,8);
		$key = substr($licensekey,0,$crc32Pos).substr($licensekey,$crc32Pos+11);
		try {
			$rsa = new Zend_Crypt_Rsa();
			$publicKey = new Zend_Crypt_Rsa_Key_Public($this->_publicKey);
			while(strlen($key)%4) $key .= '=';
			$licenseString = $rsa->decrypt(base64_decode($key),$publicKey);
			if (!$licenseString) return false;
			if (substr($licenseString,0,3) != substr($licensekey,$crc32Pos+8,3)) return false;
			$type = substr($licenseString,0,1);
			$expiredTime = hexdec(substr($licenseString,1,2));
			$extensionHash = substr($licenseString,3,8);
			if ($extensionHash != hash('crc32',$extensionName)) return false;
			$licenseDomain = trim(substr($licenseString,15));
			if (hash('crc32',substr($licensekey,0,$crc32Pos).substr($licensekey,$crc32Pos+8).$extensionName.$licenseDomain) != $crc32) return false;
			return new Varien_Object(array(
				'type'	=> $type,
				'created_date'	=> date('Y-m-d',hexdec(substr($licenseString,11,4))*24*3600),
				'expired_time'	=> $expiredTime,
				'domains'	=> $licenseDomain,
			));
		} catch (Exception $e) {
			$licenseDomain = (strlen($domain) > 38) ? substr($domain,0,38) : $domain;
			if (hash('crc32',substr($licensekey,0,$crc32Pos).substr($licensekey,$crc32Pos+8).$extensionName.$licenseDomain) != $crc32) return false;
			$type = substr($licensekey,$crc32Pos+8,1);
			$expiredTime = hexdec(substr($licensekey,$crc32Pos+9,2));
			return new Varien_Object(array(
				'type'	=> $type,
				'expired_time'	=> $expiredTime,
				'domains'	=> $licenseDomain,
			));
		}
	}
	
	private function _getSumCode($license){
		return md5('start^'.$license->getLicenseKey().$license->getExtensionCode().$license->getActiveAt().$license->getResponseCode().'$end');
	}
	
	private function _decrypt($key)
	{
		$decrypted = base64_decode($key);
		$decrypted = explode('****',$decrypted);
		if(!isset($decrypted[0]) || !isset($decrypted[1]) || !isset($decrypted[2]) || !isset($decrypted[3]))
			return false;
		if (!preg_match('/^[a-f0-9]{32}$/',$decrypted[0])) return false;
		if ($decrypted[3] < strtotime('2010-01-01 01:01:01')
			|| $decrypted[3] > strtotime('2013-01-01 01:01:01')) return false;
		return array(
			'type'	=> $decrypted[1],
			'expired_time'	=> $decrypted[2],
			'created_time'	=> $decrypted[3],
		);
	}
	
	static function getLicenseTitle($licensetype){
		$helper = Mage::helper('magenotification');
		if ($licensetype == self::TRIAL_VERSION)
			return $helper->__('Trial License');
		if ($licensetype == self::DEVELOPMENT)
			return $helper->__('Development License');
		return $helper->__('Commercial License');
	}
}