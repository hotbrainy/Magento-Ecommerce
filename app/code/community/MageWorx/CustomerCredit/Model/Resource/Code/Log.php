<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Code_Log extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('mageworx_customercredit/code_log', 'log_id');
	}
	
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $object->setActionDate(Mage::getSingleton('core/date')->gmtDate());

        return parent::_beforeSave($object);
    }
}