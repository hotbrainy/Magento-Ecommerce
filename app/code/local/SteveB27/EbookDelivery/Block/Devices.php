<?php

class SteveB27_EbookDelivery_Block_Devices extends Mage_Core_Block_Template {

    public function getExistingDevices(){
        $session = Mage::getSingleton('customer/session');
        if(!$session->isLoggedIn()){
            return array();
        }
        $devices = Mage::getResourceModel('ebookdelivery/devices_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', $session->getCustomerId());

        return $devices;
    }

}