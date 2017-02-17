<?php

class Entangled_Returns_Model_Resource_Request_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('entangled_returns/request');
    }
}