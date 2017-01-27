<?php

class SteveB27_EbookDelivery_Block_Onepage_Ebookdelivery_Options_Amazonemail extends SteveB27_EbookDelivery_Block_Onepage_Ebookdelivery
{
	public function getJs($index=0, $trigger="addAmazonEmail", $container="amazonemailContainer")
	{
		$formrow = addslashes(Mage::helper('ebookdelivery/amazonemail')->getFormInput($index));
		$formrow = str_replace("\n","\\\n",$formrow);
		$formrow = str_replace("{{index}}",'" + deviceindex + "',$formrow);
		$js  = 'var container = "' . $container . '";' . "\n";
		$js .= 'var deviceindex = ' . $index . ';' . "\n";
		$js .= "function doButtonClick(event, element) { \n";
		$js .= "	$(container).insert({bottom: \"" . $formrow . "\"}); \n";
		$js .= "    deviceindex +=1; \n";
		$js .= "} \n";
		
		$js .= '$("'.$trigger.'").on(\'click\', \'button\', doButtonClick);';
		
		return $js;
	}
	
	public function getCustomerDevices($device_type = null)
	{
		if(Mage::getSingleton('customer/session')->isLoggedIn()) {
			$customerId =  Mage::getSingleton('customer/session')->getCustomer()->getId();
			$devices = Mage::getModel('ebookdelivery/devices')->getCollection();
			$devices->addFieldToFilter('customer_id',$customerId);
			
			if(!empty($device_type)) {
				$devices->addFieldToFilter('device_type',$device_type);
			}
			
			return $devices;
		}
		
		return false;
	}
}