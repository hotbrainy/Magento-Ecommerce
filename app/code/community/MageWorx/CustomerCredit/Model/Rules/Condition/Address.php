<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Rules_Condition_Address extends MageWorx_CustomerCredit_Model_Rules_Condition_Abstract
{
    public function loadAttributeOptions() {
        $attributes = array(
            'total_amount' => Mage::helper('mageworx_customercredit')->__('Purchased amount'),
            'registration' => Mage::helper('mageworx_customercredit')->__('Registration date'),
        );
        $this->setAttributeOption($attributes);
        return $this;
    }

}
