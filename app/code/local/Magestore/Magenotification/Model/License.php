<?php

class Magestore_Magenotification_Model_License extends Mage_Core_Model_Abstract
{
    public function _construct(){
        parent::_construct();
        $this->_init('magenotification/license');
    }
    
    public function loadByLicenseExtension($licenseKey, $extensionName){
    	$item = $this->getCollection()
    		->addFieldToFilter('extension_code',$extensionName)
    		->addFieldToFilter('license_key',$licenseKey)
    		->getFirstItem();
   		if ($item && $item->getId()){
   			$this->addData($item->getData());
   		}
   		$this->setData('extension_code',$extensionName)
   			->setData('license_key',$licenseKey);
   		return $this;
    }
}