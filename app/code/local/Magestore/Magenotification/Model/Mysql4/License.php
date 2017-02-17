<?php

class Magestore_Magenotification_Model_Mysql4_License extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct(){
        $this->_init('magenotification/license', 'license_id');
    }
}