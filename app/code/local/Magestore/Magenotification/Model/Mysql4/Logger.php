<?php

class Magestore_Magenotification_Model_Mysql4_Logger extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('magenotification/logger', 'log_id');
    }
}