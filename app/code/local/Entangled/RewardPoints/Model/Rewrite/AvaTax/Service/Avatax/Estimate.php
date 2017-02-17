<?php

class Entangled_RewardPoints_Model_Rewrite_AvaTax_Service_Avatax_Estimate extends OnePica_AvaTax_Model_Service_Avatax_Estimate {


    /**
     * Makes a Line object from a product item object
     *
     * @param Varien_Object|Mage_Sales_Model_Quote_Item $item
     * @return int|bool
     */
    protected function _newLine($item)
    {
        if (!$item->getId()) {
            $this->setCanSendRequest(false);

            return $this;
        }

        $this->_addGwItemsAmount($item);
        if ($this->isProductCalculated($item)) {
            return false;
        }
        $product = $this->_getProductByProductId($item->getProductId());
        $taxClass = $this->_getTaxClassCodeByProduct($product);
        $price = $item->getBaseRowTotal();

        if ($this->_getTaxDataHelper()->applyTaxAfterDiscount($item->getStoreId())) {
            $address = $item->getQuote()->getBillingAddress();
            $credit = $address->getCustomerCreditAmount();
            $total = $address->getSubtotal() ? $address->getSubtotal() : $address->getOriginalSubtotal();
            $discount = $credit/$total;
            $creditDiscountAmount = $item->getBaseRowTotal() * $discount;
            $price = $item->getBaseRowTotal() - $item->getBaseDiscountAmount() - $creditDiscountAmount;
        }

        $lineNumber = count($this->_lines);
        $line = new Line();
        $line->setNo($lineNumber);
        $line->setItemCode(
            $this->_getCalculationHelper()->getItemCode(
                $this->_getProductForItemCode($item),
                $item->getStoreId()
            )
        );
        $line->setDescription($item->getName());
        $line->setQty($item->getTotalQty());
        $line->setAmount($price);
        $line->setItemId($item->getId());
        $line->setDiscounted(
            (float)($item->getDiscountAmount() + $creditDiscountAmount) && $this->_getTaxDataHelper()->applyTaxAfterDiscount($item->getStoreId())
        );

        if ($this->_getTaxDataHelper()->priceIncludesTax($item->getStoreId())) {
            $line->setTaxIncluded(true);
        }

        if ($taxClass) {
            $line->setTaxCode($taxClass);
        }
        $ref1Value = $this->_getRefValueByProductAndNumber($product, 1, $item->getStoreId());
        if ($ref1Value) {
            $line->setRef1($ref1Value);
        }
        $ref2Value = $this->_getRefValueByProductAndNumber($product, 2, $item->getStoreId());
        if ($ref2Value) {
            $line->setRef2($ref2Value);
        }

        $this->_lines[$lineNumber] = $line;
        $this->_lineToLineId[$lineNumber] = $item->getId();

        return $lineNumber;
    }
}