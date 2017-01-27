<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Code_Log extends Mage_Core_Model_Abstract
{
    const ACTION_TYPE_CREATED = 0;
    const ACTION_TYPE_UPDATED = 1;
    const ACTION_TYPE_USED    = 2;

    protected function _construct() {
        $this->_init('mageworx_customercredit/code_log');
    }

    public function getActionTypesOptions() {
        return array(
            self::ACTION_TYPE_CREATED => Mage::helper('mageworx_customercredit')->__('Created'),
            self::ACTION_TYPE_UPDATED => Mage::helper('mageworx_customercredit')->__('Updated'),
            self::ACTION_TYPE_USED    => Mage::helper('mageworx_customercredit')->__('Used'),
        );
    }

    protected function _beforeSave() {
        if (!$this->hasCodeModel()) Mage::throwException(Mage::helper('mageworx_customercredit')->__('Recharge code hasn\'t assigned.'));

        $this->setCodeId($this->getCodeModel()->getId());
        $this->setComment($this->_getComment());
        return parent::_beforeSave();
    }

    protected function _getComment() {
        $comment = '';
        switch ($this->getActionType()) {
            case self::ACTION_TYPE_CREATED :
            case self::ACTION_TYPE_UPDATED :
                break;
            case self::ACTION_TYPE_USED :
                if ($customerId = $this->getCodeModel()->getCustomerId()) {
                    $comment =  Mage::helper('mageworx_customercredit')->__('By Customer #%s', $customerId);
                }
                break;
            default :
                Mage::throwException(Mage::helper('mageworx_customercredit')->__('Unknown log action type.'));
                break;
        }
        return $comment;
    }
}