<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Rules extends Mage_Core_Model_Mysql4_Abstract
{
	protected function _construct()
	{
		$this->_init('mageworx_customercredit/rules', 'rule_id');
	}
	
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
        $date = Mage::app()->getLocale()->date();
        $dateFull = clone $date;
        $date->setHour(0)
            ->setMinute(0)
            ->setSecond(0);
        if (!$object->getFromDate()) {
            $object->setFromDate($date);
        }
        if ($object->getFromDate() instanceof Zend_Date) {
            $object->setFromDate($object->getFromDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }

        if (!$object->getToDate()) {
            $object->setToDate(new Zend_Db_Expr('NULL'));
        }
        else {
            if ($object->getToDate() instanceof Zend_Date) {
                $object->setToDate($object->getToDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            }
        }
        
        if (!$object->getId()) {
            $object->setCreatedDate($dateFull);
        }
        if ($object->getCreatedDate() instanceof Zend_Date) {
            $object->setCreatedDate($object->getCreatedDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }
        $object->setUpdatedDate($dateFull);
        if ($object->getUpdatedDate() instanceof Zend_Date) {
            $object->setUpdatedDate($object->getUpdatedDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }
        return parent::_beforeSave($object);
	}
}