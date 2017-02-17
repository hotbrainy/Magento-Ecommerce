<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_System_Config_Backend_Expiration extends Mage_Core_Model_Config_Data
{    
    
    protected function _afterSave() {
        parent::_afterSave();
        if(($this->getValue()!=$this->getOldValue()) && Mage::getStoreConfig('mageworx_customercredit/expiration/expiration_enable')) {
            $model = Mage::getModel('cron/schedule');
            $model->setJobCode('credit_expiration_date_refresh')
                  ->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING)
                  ->setCreatedAt(now())
                  ->setScheduledAt(date("Y-m-d h:i:s",time()+60));
            $model->save();
        }
    }
}