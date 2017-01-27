<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Creditmemo_Total_Customercredit extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo) {
        if (!Mage::helper('mageworx_customercredit')->isEnabled()) return $this;
        
        $order = $creditmemo->getOrder();
        if (!$order->getBaseCustomerCreditAmount() || $order->getBaseCustomerCreditInvoiced()==0) return $this;
        
        $invoiceBaseRemainder = $order->getBaseCustomerCreditInvoiced() - $order->getBaseCustomerCreditRefunded();
        if ($invoiceBaseRemainder<0) return $this;        
        
        $invoiceRemainder = $order->getCustomerCreditInvoiced() - $order->getCustomerCreditRefunded();                
        
        $used = $baseUsed = 0;
        if ($invoiceBaseRemainder < $creditmemo->getBaseGrandTotal()) {
            $used = $invoiceRemainder;
            $baseUsed = $invoiceBaseRemainder;
            
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal()-$used);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()-$baseUsed);
            
            
        } else {
            $used = $creditmemo->getGrandTotal();
            $baseUsed = $creditmemo->getBaseGrandTotal();
            
            $creditmemo->setBaseGrandTotal(0);
            $creditmemo->setGrandTotal(0);
            $creditmemo->setAllowZeroGrandTotal(true);
        }
        $creditmemo->setCustomerCreditAmount($used);
        $creditmemo->setBaseCustomerCreditAmount($baseUsed);
        
        return $this;
    }
}