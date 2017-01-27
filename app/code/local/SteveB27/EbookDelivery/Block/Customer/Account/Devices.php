<?php

class SteveB27_EbookDelivery_Block_Customer_Account_Devices extends Mage_Core_Block_Template
{
	
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $session = Mage::getSingleton('customer/session');
        $devices = Mage::getResourceModel('ebookdelivery/devices_collection')
			->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $session->getCustomerId());
            
        $this->setItems($devices);
	}

	public function getDeviceType($item){
        if($item->getDeviceType() == "amazonemail"){
            return "Kindle";
        }
    }

    public function getRemoveUrl($item){
        return $this->getUrl("ebookdelivery/devices/remove",array("id"=>$item->getId()));
    }

    public function getAddDeviceUrl(){
        return $this->getUrl("ebookdelivery/devices/add");
    }
}