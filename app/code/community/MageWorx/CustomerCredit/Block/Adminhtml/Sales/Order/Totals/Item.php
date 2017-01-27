<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Sales_Order_Totals_Item extends Mage_Adminhtml_Block_Sales_Order_Totals_Item
{
    public function getCanDisplayCustomerCreditRefunded()
    {
        return ($this->getOrder()->getCustomerCreditAmount() && $this->getOrder()->getCustomerCreditRefunded());
    }
}