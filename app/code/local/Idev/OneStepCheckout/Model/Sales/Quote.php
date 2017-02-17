<?php
class  Idev_OneStepCheckout_Model_Sales_Quote extends Mage_Sales_Model_Quote
{

    /**
     * Collect totals patched for magento issue #26145
     *
     * @return Mage_Sales_Model_Quote
     */
    public function collectTotals()
    {

        /**
         * patch for magento issue #26145
         */
        if (!$this->getTotalsCollectedFlag()) {

            $items = $this->getAllItems();

            foreach($items as $item){
                $item->setData('calculation_price', null);
                $item->setData('original_price', null);
            }

        }

        parent::collectTotals();
        return $this;

    }

    /**
     * Check is allow Guest Checkout
     *
     * @deprecated after 1.4 beta1 it is checkout module responsibility
     * @return bool
     */
    public function isAllowedGuestCheckout()
    {
        $persistentHelper  = Mage::helper('onestepcheckout')->getPersistentHelper();
        if(is_object($persistentHelper)){
            //persistant checkout disables guest checkout
            if($persistentHelper->isPersistent()){
                return true;
            } else {
                return parent::isAllowedGuestCheckout();
            }
        }
    }

    /**
     * Merge quotes
     *
     * @param   Mage_Sales_Model_Quote $quote
     * @return  Mage_Sales_Model_Quote
     */
    public function merge(Mage_Sales_Model_Quote $quote)
    {
        Mage::dispatchEvent(
            $this->_eventPrefix . '_merge_before',
            array(
                $this->_eventObject=>$this,
                'source'=>$quote
            )
        );

        $customerSession = Mage::getSingleton("customer/session");
        $customHelper = Mage::helper('entangled_custom');

        /** @var Mage_Sales_Model_Quote_Item $item */
        foreach ($quote->getAllVisibleItems() as $item) {
            if($customerSession->isLoggedIn()){
                $product = $item->getProduct();
                if($customHelper->isRepeatedProduct($product)){
                    $message = "You have already purchased ".$product->getName()."! Please go to my library to re download or re-read.";
                    //$customerSession->addError($message);
                    continue;
                }
            }

            $found = false;
            /** @var Mage_Sales_Model_Quote_Item $quoteItem */
            foreach ($this->getAllItems() as $quoteItem) {
                if ($quoteItem->compare($item)) {
                    $max = Mage::getModel('cataloginventory/stock_item')->loadByProduct($quoteItem->getProduct())->getMaxSaleQty();
                    if(($quoteItem->getQty()+$item->getQty()) <= $max){
                        $quoteItem->setQty($quoteItem->getQty() + $item->getQty());
                    }else{
                        $quoteItem->setQty($max);
                    }
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $newItem = clone $item;
                $this->addItem($newItem);
                if ($item->getHasChildren()) {
                    foreach ($item->getChildren() as $child) {
                        $newChild = clone $child;
                        $newChild->setParentItem($newItem);
                        $this->addItem($newChild);
                    }
                }
            }
        }

        /**
         * Init shipping and billing address if quote is new
         */
        if (!$this->getId()) {
            $this->getShippingAddress();
            $this->getBillingAddress();
        }

        if ($quote->getCouponCode()) {
            $this->setCouponCode($quote->getCouponCode());
        }

        Mage::dispatchEvent(
            $this->_eventPrefix . '_merge_after',
            array(
                $this->_eventObject=>$this,
                'source'=>$quote
            )
        );

        return $this;
    }
}
