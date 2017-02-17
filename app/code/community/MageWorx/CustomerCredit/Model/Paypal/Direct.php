<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Paypal_Direct extends Mage_Paypal_Model_Direct
{
    
    protected function _placeOrder(Mage_Sales_Model_Order_Payment $payment, $amount) {
        $order = $payment->getOrder();
        
        // get credit amount
        $baseCustomerCreditAmount = $order->getBaseCustomerCreditAmount();
        $customerCreditAmount = $order->getCustomerCreditAmount();               
        
        // apply credit
        if ($baseCustomerCreditAmount > $order->getBaseSubtotal()) {
            // fix if credit in order > Subtotal
            $order->setBaseShippingAmount(0)->setShippingAmount(0);
            $order->setBaseTaxAmount(0)->setTaxAmount(0);
            $order->setBaseSubtotal($order->getBaseGrandTotal())->setSubtotal($order->getGrandTotal());
        } elseif ($baseCustomerCreditAmount>0) {
            $order->setBaseDiscountAmount(abs($order->getBaseDiscountAmount()) + $baseCustomerCreditAmount);
            $order->setDiscountAmount(abs($order->getDiscountAmount()) + $customerCreditAmount);
        }
        return parent::_placeOrder($payment, $amount);
    }    
}
