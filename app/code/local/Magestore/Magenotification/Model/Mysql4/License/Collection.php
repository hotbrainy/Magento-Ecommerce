<?php

class Magestore_Magenotification_Model_Mysql4_License_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct(){
        parent::_construct();
        $this->_init('magenotification/license');
    }
}