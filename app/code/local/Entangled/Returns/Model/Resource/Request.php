<?php
class Entangled_Returns_Model_Resource_Request extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('entangled_returns/request','id');
    }
}