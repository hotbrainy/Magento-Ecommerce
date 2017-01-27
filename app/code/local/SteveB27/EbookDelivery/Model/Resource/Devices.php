<?php

class SteveB27_EbookDelivery_Model_Resource_Devices extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('ebookdelivery/devices', 'id');
    }
}