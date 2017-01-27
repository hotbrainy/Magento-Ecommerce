<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Widget_Grid_Column_Renderer_Currencychange
extends MageWorx_CustomerCredit_Block_Adminhtml_Widget_Grid_Column_Renderer_Currency
{
    public function render(Varien_Object $row)
    {
        $value = $this->_getValue($row);
        $value = Mage::helper('mageworx_customercredit')->getAddedOrDeductedValue($value);
        return $value;
    }
}