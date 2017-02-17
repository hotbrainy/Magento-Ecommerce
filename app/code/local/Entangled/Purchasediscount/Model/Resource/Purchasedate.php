<?php
class Entangled_Purchasediscount_Model_Resource_Purchasedate extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
    {
        $this->_init('purchasediscount/purchasedate','purchase_id');
    }
}