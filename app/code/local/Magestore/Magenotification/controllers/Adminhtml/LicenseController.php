<?php

class Magestore_Magenotification_Adminhtml_LicenseController extends Mage_Adminhtml_Controller_Action
{
	public function upgradeAction()
	{
		$licensekey = $this->getRequest()->getParam('licensekey');
		$licensetype = $this->getRequest()->getParam('licensetype');
		$redirectUrl = Magestore_Magenotification_Model_Keygen::SERVER_URL.'licensemanager/license/upgrade/licensekey/'.$licensekey.'/licensetype/'.$licensetype;
		$this->_redirectUrl($redirectUrl);	
	}
	
	public function purchaseAction()
	{
		$extension = $this->getRequest()->getParam('extension');
		$redirectUrl = Magestore_Magenotification_Model_Keygen::SERVER_URL.'licensemanager/license/purchase/extension/'.$extension;
		$this->_redirectUrl($redirectUrl);
	}
	
	public function viewpriceAction()
	{
		$licensekey = $this->getRequest()->getParam('licensekey');
		$licensetype = $this->getRequest()->getParam('licensetype');
		$upgradePrice = Mage::helper('magenotification/license')->getUpgradePrice($licensekey,$licensetype);
		$html = '<b>'.$upgradePrice.'</b>';
		$html .= ' '.Mage::helper('magenotification')->__('for upgrade to');
		$html .= ' '. Mage::getModel('magenotification/keygen')->getLicenseTitle($licensetype);
		$html .= '<br/><br/><button style="" onclick="updateLicensePurchase(\''.$licensekey.'\')" class="scalable add" type="button" >
				 <span>'.Mage::helper('magenotification')->__('Upgrade Now').'</span></button>';
		echo $html;
	}
}