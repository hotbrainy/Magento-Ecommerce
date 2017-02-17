<?php
class Magestore_Magenotification_Block_Adminhtml_License_Updateform 
	extends Mage_Adminhtml_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('magenotification/license/updateform.phtml');
	}
	
	public function getUpdateUrl()
	{
		//return $this->getUrl('magenotification/adminhtml_license/upgrade',array('licensekey'=>$this->getLicensekey(),'_secure'=>true));
		return $this->getUrl('magenotification/adminhtml_license/upgrade',array('_secure'=>true));
	}
	
	public function getViewPriceUrl()
	{
		//return $this->getUrl('magenotification/adminhtml_license/viewprice',array('licensekey'=>$this->getLicensekey(),'_secure'=>true));
		return $this->getUrl('magenotification/adminhtml_license/viewprice',array('_secure'=>true));
	}
	
	public function getLicenseTypeOption()
	{	
		$list = array();
		$list[Magestore_Magenotification_Model_Keygen::DOMAIN1] = $this->__('1 Domains');
		$list[Magestore_Magenotification_Model_Keygen::DOMAIN2] = $this->__('2 Domains');
		$list[Magestore_Magenotification_Model_Keygen::DOMAIN5] = $this->__('5 Domains');
		$list[Magestore_Magenotification_Model_Keygen::DOMAIN10] = $this->__('10 Domains');
		$list[Magestore_Magenotification_Model_Keygen::UNLIMITED] = $this->__('Unlimited Domain');
		$list[Magestore_Magenotification_Model_Keygen::DEVELOPER] = $this->__('Developer');
		foreach($list as $key=>$item){
			if($key <= $this->getCurrentLicenseType()){
				unset($list[$key]);
			}
		}
		return $list;
	}
}