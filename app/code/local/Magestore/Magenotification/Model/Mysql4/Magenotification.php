<?php

class Magestore_Magenotification_Model_Mysql4_Magenotification extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('magenotification/magenotification', 'magenotification_id');
    }
}