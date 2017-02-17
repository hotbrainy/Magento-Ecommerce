<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Sales_Order_Totals_Customercredit extends Mage_Core_Block_Abstract
{
    public function initTotals() {
        $customerCreditAmount = $this->getParentBlock()->getSource()->getCustomerCreditAmount();
        if ($customerCreditAmount>0) {
            $customercreditTotal = new Varien_Object(array(
                'code'      => 'customer_credit_amount',
                'field'  => 'customer_credit_amount',
                'label'  => Mage::helper('mageworx_customercredit')->__('Internal Credit'),
                'value'  => -$customerCreditAmount,
            ));
            $this->getParentBlock()->addTotalBefore($customercreditTotal, 'grand_total');
        }
        return $this;
    }
}