<?php


class SteveB27_EbookDelivery_Model_Resource_Devices_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('ebookdelivery/devices');
    }
}