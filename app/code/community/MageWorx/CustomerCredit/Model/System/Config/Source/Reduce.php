<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_System_Config_Source_Reduce extends Mage_Core_Model_Config_Data
{
    const MODE_AS_DISCOUNT         = '1';
    const MODE_AS_PAYMENT_METHOD   = '0';

    public function toOptionArray() {
        $helper = Mage::helper('mageworx_customercredit');
        $options = array(
            array('value'=>self::MODE_AS_DISCOUNT, 'label'=> $helper->__('As Discount')),
            array('value'=>self::MODE_AS_PAYMENT_METHOD, 'label'=> $helper->__('As Payment Method')),
        );
        return $options;
    }

}