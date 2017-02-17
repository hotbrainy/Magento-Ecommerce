<?php

class Entangled_RewardPoints_Model_Rewrite_AvaTax_Sales_Quote_Address_Total_Tax extends OnePica_AvaTax_Model_Sales_Quote_Address_Total_Tax {

    /**
     * Reset address values
     *
     * @param \Mage_Sales_Model_Quote_Address $address
     * @return $this
     */
    protected function _resetAddressValues(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setTotalAmount($this->getCode(), 0);
        $address->setBaseTotalAmount($this->getCode(), 0);

        $address->setTaxAmount(0);
        $address->setBaseTaxAmount(0);
        $address->setShippingTaxAmount(0);
        $address->setBaseShippingTaxAmount(0);

        $address->setOriginalSubtotal($address->getSubtotal());
        $address->setSubtotal(0);
        $address->setSubtotalInclTax(0);
        $address->setBaseSubtotalInclTax(0);
        $address->setTotalAmount('subtotal', 0);
        $address->setBaseTotalAmount('subtotal', 0);

        $address->setGwItemsTaxAmount(0);
        $address->setGwItemsBaseTaxAmount(0);
        $address->setGwBaseTaxAmount(0);
        $address->setGwTaxAmount(0);
        $address->setGwCardBaseTaxAmount(0);
        $address->setGwCardTaxAmount(0);

        return $this;
    }

}