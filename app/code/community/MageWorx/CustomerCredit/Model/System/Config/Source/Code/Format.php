<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_System_Config_Source_Code_Format
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'num', 'label'=>Mage::helper('mageworx_customercredit')->__('Numeric')),
            array('value'=>'alphanum', 'label'=>Mage::helper('mageworx_customercredit')->__('Alphanumeric')),
            array('value'=>'alphabet', 'label'=>Mage::helper('mageworx_customercredit')->__('Alphabetical')),
        );
    }
}