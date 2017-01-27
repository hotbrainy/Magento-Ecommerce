<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
class Amasty_Shopby_Model_Mysql4_Value extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('amshopby/value', 'value_id');
    }

	public function getFeaturedOptionsIds()
	{
		$db = $this->getReadConnection();
		$select = $db->select()->from($this->getMainTable(), 'option_id')->where('is_featured = 1');
		return $db->fetchCol($select);
	}
}