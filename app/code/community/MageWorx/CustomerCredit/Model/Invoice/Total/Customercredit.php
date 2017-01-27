<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Invoice_Total_Customercredit extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        if (!Mage::helper('mageworx_customercredit')->isEnabled())
        {
            return $this;
        }
        
        $order = $invoice->getOrder();
//        echo "<pre>"; print_r($order->toArray()); exit;
        if (!$order->getBaseCustomerCreditAmount() || ($order->getBaseCustomerCreditAmount() == $order->getBaseCustomerCreditInvoiced()) ||
           ($order->getGrandTotal()-$order->getSubtotal()==$order->getCustomerCreditAmount())
                )
        {
            return $this;
        }
        
        $invoiceBaseRemainder = $order->getBaseCustomerCreditAmount() - $order->getBaseCustomerCreditInvoiced();
        $invoiceRemainder     = $order->getCustomerCreditAmount() - $order->getCustomerCreditInvoiced();
        $used = $baseUsed = 0;
        $used = $invoiceRemainder;
        $baseUsed = $invoiceBaseRemainder;            
            
        if ($invoiceBaseRemainder < $invoice->getBaseGrandTotal()) {
            $invoice->setGrandTotal($invoice->getGrandTotal()-$used);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()-$baseUsed);
        }elseif($invoiceBaseRemainder && $invoice->getBaseGrandTotal()) {
            $invoice->setGrandTotal($invoice->getGrandTotal());
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal());
        } else {
            $invoice->setBaseGrandTotal(0);
            $invoice->setGrandTotal(0);
        }
        
        $invoice->setCustomerCreditAmount($used);
        $invoice->setBaseCustomerCreditAmount($baseUsed);
        
        return $this;
    }
}