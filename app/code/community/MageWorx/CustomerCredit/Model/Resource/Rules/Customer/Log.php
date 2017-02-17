<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Rules_Customer_Log extends MageWorx_CustomerCredit_Model_Resource_Rules_Customer
{
	protected function _construct()
	{
		$this->_init('mageworx_customercredit/rules_customer_log', 'id');
	}
}