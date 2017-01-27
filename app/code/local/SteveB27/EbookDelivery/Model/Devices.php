<?php

class SteveB27_EbookDelivery_Model_Devices extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ebookdelivery/devices');
    }
}