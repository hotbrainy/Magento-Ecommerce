<?php

class Entangled_Custom_Model_Rewrite_CustomerCredit_Rules_Handler extends MageWorx_CustomerCredit_Model_Rules_Handler {

    /**
     * Calculate credit value
     * @param MageWorx_CustomerCredit_Model_Rules $rule
     * @return MageWorx_CustomerCredit_Model_Rules
     */
    protected function _calculateCredit($rule) {
        // if qty dependent
        if (isset($rule['qty_dependent']) && ($rule['qty_dependent']==1)) {
            $rule['credit'] = $rule['credit'] * $this->_ruleQty;
        }
        if(isset($this->_order) && (strpos($rule['credit'],'%')!==false)) {

            $rule['credit'] = (int) str_replace('%', '', $rule['credit']);
            $total = (float)($this->_order->getBaseSubtotal() - $this->_order->getBaseCustomerCreditAmount() + $this->_order->getBaseDiscountAmount());
            if(!$total) {
                $total = $this->_order->getSubtotalInvoiced();
            }
            /** @var Mage_Sales_Model_Order_Item $item */
            foreach($this->_order->getItemsCollection() as $item){
                if($item->getSku() == Entangled_Purchasediscount_Helper_Data::DISCOUNT_SKU){
                    $total -= $item->getRowTotal();
                }
            }
            $rule['credit'] = round($total*$rule['credit']/100,2);
        }

        return $rule;
    }
}