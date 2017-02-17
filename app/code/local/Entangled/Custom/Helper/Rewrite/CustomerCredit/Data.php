<?php

class Entangled_Custom_Helper_Rewrite_CustomerCredit_Data extends MageWorx_CustomerCredit_Helper_Data {

    /**
     * @param $value
     * @return float
     */
    public function getValueExchangeRateDivided($value)
    {
        return $this->floor_dec((float)($value / $this->getExchangeRate()),2);
    }

    /**
     * @param $value
     * @return float
     */
    public function getValueExchangeRateMultiplied($value)
    {
        return (float)($value * $this->getExchangeRate());
    }

    public function floor_dec($number,$precision = 2,$separator = '.') {
        $numberpart=explode($separator,$number);
        $numberpart[1]=substr_replace($numberpart[1],$separator,$precision,0);
        if($numberpart[0]>=0) {
            $numberpart[1]=substr(floor('1'.$numberpart[1]),1);
        } else {
            $numberpart[1]=substr(ceil('1'.$numberpart[1]),1);
        }
        $ceil_number= array($numberpart[0],$numberpart[1]);
        return implode($separator,$ceil_number);
    }

    public function getMembershipPriceWithTax(){
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach($quote->getAllItems() as $item){
            if($item->getSku() == Entangled_Purchasediscount_Helper_Data::DISCOUNT_SKU){
                return $item->getRowTotalInclTax();
            }
        }
    }

    /**
     * Get partitial payment type
     * @param Mage_Sales_Model_Quote $quote
     * @param int $customerId
     * @param int $websiteId
     * @return boolean|int
     *  -3 - can't apply credits
     *  -2 - hide customer credit
     *  -1 - no balabce checkbox
     *  0 - no balance radio
     *  1 - checkbox (partial payment)
     *  2 - radio (full payment)
     */
    public function isPartialPayment($quote, $customerId = null, $websiteId = null)
    {
        if (!$this->isShowCustomerCredit()) {
            return -2;
        }
        if (!$quote) {
            return -2;
        }

        if (Mage::app()->getStore()->isAdmin()) {
            $customerId = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();
        }

        if (!$customerId) {
            return false;
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if (!$customer->isEmpty()) {
                $customerId = $customer->geId();
            } else {
                return false;
            }
        }
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $value = $this->getCreditValue($customer);
        $value = $this->getValueExchangeRateDivided($value);
        $isEnabledPartialPayment = $this->isEnabledPartialPayment();
        if ($value == 0) {
            if ($isEnabledPartialPayment) return -1; else return 0;
        }

        // check apply credits
        $productConditionsPrice = $this->checkApplyCreditsSum($quote, $customerId, $websiteId);
        if (sizeof($productConditionsPrice) > 0 && !array_sum($productConditionsPrice)) {
            return -3;
        }

        if (Mage::getSingleton('customer/session')->getData('customer_credit_rule')) {
            return 1;
        }
        if (Mage::app()->getStore()->isAdmin()) {
            $allItems = $quote->getAllItems();
            $productIds = array();
            foreach ($allItems as $item) {
                $productIds[] = $item->getProductId();
            }
        } else {
            $productIds = Mage::getSingleton('checkout/cart')->getProductIds();
        }

        $addressType = Mage_Sales_Model_Quote_Address::TYPE_BILLING;
        $creditProductSku = $this->getCreditProductSku();
        foreach ($productIds as $productId) {
            $product = Mage::getModel('catalog/product')->load($productId);
            if (!$product) continue;
            // is credit product - no credit!
            if ($creditProductSku && $product->getSku() == $creditProductSku) return 0;

            $productTypeId = $product->getTypeId();
            if ($productTypeId != 'downloadable' && !$product->isVirtual()) {
                $addressType = Mage_Sales_Model_Quote_Address::TYPE_SHIPPING;
                break;
            }
        }

        //shipping or billing
        if ($addressType == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING) {
            $addresses = $quote->getAllShippingAddresses();
        } else {
            $addresses = $this->getAllBillingAddresses($quote);
        }

        $subtotal = 0;
        $shipping = 0;
        $tax = 0;
        $grandTotal = 0;
        $tail = 0;
        foreach ($addresses as $address) {
            if($address->getAddressType() == "shipping") {
                continue;
            }
            ///////
            $subtotal = floatval($address->getBaseSubtotalWithDiscount() - $address->getMwRewardpointDiscount()); //$address->getBaseSubtotal();
            $shipping = floatval($address->getBaseShippingAmount() - $address->getBaseShippingTaxAmount());
            $tax = floatval($address->getBaseTaxAmount());
            $discount = $address->getBaseDiscountAmount();

            $grandTotal = floatval($quote->getBaseGrandTotal() + $address->getBaseCustomerCreditAmount());
            if ($grandTotal == 0) $grandTotal = floatval(array_sum($address->getAllBaseTotalAmounts()));
            if ($grandTotal == 0) $grandTotal = $subtotal + $shipping + $tax;
//            echo $subtotal.'|'.$shipping.'|'.$tax.'|='.$grandTotal.'<br/>';
            $tail = $grandTotal;
            ///////
        }
        $creditTotals = $this->getCreditTotals();
        if (count($creditTotals) < 3) {
            $amount = 0;
            foreach ($creditTotals as $field) {
                switch ($field) {
                    case 'subtotal':
                        $amount += $subtotal;
                        $tail -= $subtotal;
                        break;
                    case 'shipping':
                        $amount += $shipping;
                        $tail -= $shipping;
                        break;
                    case 'tax':
                        $amount += $tax;
                        $tail -= $tax;
                        break;
                    case 'fees':
                        $baseCreditLeft += $address->getBaseMultifeesAmount();
                        $creditLeft += $address->getMultifeesAmount();
                        break;
                }
            }
        } else {
            $amount = $grandTotal;
            $tail = 0;
        }
        $tail -= $tax;

        if (sizeof($productConditionsPrice) > 0) {
            $sum = array_sum($productConditionsPrice);
            $baseCreditLeft = $sum;
            $creditLeft = $sum;
        }

        if($quote->getCustomerCreditAmount()){
            $amount += $quote->getCustomerCreditAmount();
        }

        $amount = round($amount, 2);
        $tail = round($tail, 2);
//        echo $amount.'|'.$tail.'|'.$value; //exit;

        if ($value >= $amount && $tail == 0) {
            $maxCredit = $this->getMinOrderAmount();
            if ($maxCredit && ($value > ($amount * $maxCredit / 100))) {

                if ($isEnabledPartialPayment) return 1; else return 0;
            }
            return 2;
        } else {
            if ($isEnabledPartialPayment) return 1; else return 0;
        }
    }

    public function isFirstTime(){
        $session = Mage::getSingleton("customer/session");
        if($session->isLoggedIn()){
            /** @var Mage_Sales_Model_Resource_Order_Collection $orders */
            $orders = Mage::getModel("sales/order")->getCollection()->addFieldToFilter("customer_id",$session->getCustomerId());

            return $orders->count() == 0;
        }else{
            return true;
        }
    }

    public function getAllBillingAddresses($quote)
    {
        $addresses = array();
        foreach ($quote->getAddressesCollection() as $address) {
            if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_BILLING
                && !$address->isDeleted()
            ) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

}