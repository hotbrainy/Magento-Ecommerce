<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Sales_Order_View_Tab_Info extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Info
{
    public function getPaymentHtml()
    {
        $paymentHtml =  parent::getPaymentHtml();
        $_order = $this->getOrder();

        if($_order->getCustomerCreditAmount()>0 && $_order->getPayment()->getMethod() != 'customercredit') {
            $paymentHtml = $this->__('Internal Credit') . ' + ' . $paymentHtml;
        }
        return $paymentHtml;
    }
}
