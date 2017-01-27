<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Paypal_Standard extends Mage_Paypal_Model_Standard {

    public function getStandardCheckoutFormFields() {
        $rArr = parent::getStandardCheckoutFormFields();        
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
        $credit = $order->getBaseCustomerCreditAmount();

        if ($credit > $order->getBaseSubtotal()) {
            // fix if credit in order > Subtotal
            unset($rArr['tax']);
            unset($rArr['shipping']);
            $rArr['amount'] = sprintf('%.2f', $order->getBaseGrandTotal());
        } else if (isset($rArr['discount_amount'])) {
            $rArr['discount_amount'] = sprintf('%.2f', abs($rArr['discount_amount']) + $credit);
        } elseif (isset($rArr['discount_amount_cart'])) {
            $rArr['discount_amount_cart'] = sprintf('%.2f', abs($rArr['discount_amount_cart']) + $credit);
        } else {
            $rArr['discount_amount'] = sprintf('%.2f', $credit);
        }        
        if($rArr['amount']>$order->getSubtotal()) {
                $rArr['amount'] -= $rArr['shipping'];
        }
        //print_r($rArr); exit;        
        return $rArr;
    }
}