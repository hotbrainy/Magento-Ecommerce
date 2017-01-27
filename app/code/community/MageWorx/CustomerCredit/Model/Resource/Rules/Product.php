<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Rules_Product extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('salesrule/rule_product', 'product_rule_id');
    }
}