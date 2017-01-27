<?php

class SteveB27_EbookDelivery_Block_Customer_Account_Add extends SteveB27_EbookDelivery_Block_Customer_Account_Devices
{

    public function getBackUrl(){
        return $this->getUrl("ebookdelivery/devices/index");
    }

    public function getFormActionUrl(){
        return $this->getUrl("ebookdelivery/devices/addPost");
    }

}