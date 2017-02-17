<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_System_Config_Source_Sync extends Mage_Core_Model_Config_Data
{    
    const ACTION_TYPE_APPEND    = 'append';
    const ACTION_TYPE_REPLACE   = 'replace';
    
    public function toOptionArray() {
       
        $options = array(
            array('value'=>self::ACTION_TYPE_APPEND, 'label'=>Mage::helper('mageworx_customercredit')->__('Append')),
            array('value'=>self::ACTION_TYPE_REPLACE, 'label'=>Mage::helper('mageworx_customercredit')->__('Replace')),
        );
        return $options;
    }    
    
}