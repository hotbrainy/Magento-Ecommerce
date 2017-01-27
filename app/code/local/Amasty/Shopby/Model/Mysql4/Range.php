<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * @author Amasty
 */ 
class Amasty_Shopby_Model_Mysql4_Range extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('amshopby/range', 'range_id');
    }
}