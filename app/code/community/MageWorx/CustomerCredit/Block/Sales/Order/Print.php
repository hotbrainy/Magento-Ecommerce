<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Sales_Order_Print extends Mage_Sales_Block_Order_Print {

    public function getPaymentInfoHtml() {
        $paymentHtml =  parent::getChildHtml('payment_info');
        $_order = $this->getOrder();
        if($_order->getCustomerCreditAmount()>0 && $_order->getPayment()->getMethod() != 'customercredit') {
            $paymentHtml =$this->__('Internal Credit') . ' + ' . $paymentHtml;
        }
        return $paymentHtml;
    }

}

