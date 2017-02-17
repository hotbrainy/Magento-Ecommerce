<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Payment_Form extends Mage_Payment_Block_Form {

    protected function _construct() {
        parent::_construct();
        $this->setTemplate('mageworx/customercredit/payment/form.phtml');
    }

    /**
     * @return float
     * @throws Mage_Core_Exception
     */
    public function getCreditValue() {

        /**
         * @var Mage_Sales_Model_Quote $quote
         * @var MageWorx_CustomerCredit_Helper_Data $helper
         * @var Mage_Core_Model_App $app
         */
        $app = Mage::app();
        $quote  = Mage::getSingleton('checkout/cart')->getQuote();
        $helper = Mage::helper('mageworx_customercredit');
        $websiteId = Mage::app()->getWebsite()->getId();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $customerId = $customer->getId();

        if ($app->getStore()->isAdmin()) {
            $quote  = Mage::getSingleton('adminhtml/sales_order_create')->getQuote();
            $customerId = $quote->getCustomerId();
            if($app->isSingleStoreMode()) {
                $websites = Mage::getResourceModel('core/website_collection');
                $websiteId = $websites->getFirstItem()->getId();
            }
            $quote->collectTotals()->save();
        }
        $subtotal = $quote->getSubtotalWithDiscount();
        /** @var Mage_Sales_Model_Quote_Address $address */
        $address = $helper->getSalesAddress($quote);
        $subtotal -= $address->getMwRewardpointDiscount();
        $productConditionsPrice = $helper->checkApplyCreditsSum($quote,$customerId,$websiteId);

        $shipping = floatval($address->getShippingAmount() - $address->getShippingTaxAmount());
        $tax = floatval($address->getTaxAmount());
        $creditTotals = $helper->getCreditTotals();

        $shippingAdded = false;
        $taxAdded = false;

        if (count($creditTotals)<=3) {
            foreach ($creditTotals as $field) {
                switch ($field) {
                    case 'shipping':
                        $subtotal += $shipping;
                        $shippingAdded = true;
                        break;
                    case 'tax':
                        $subtotal += $tax;
                        $taxAdded = true;
                        break;
                    case 'fees':
                        $subtotal += $address->getMultifeesAmount();
                        break;
                }
            }
        }
        if(sizeof($productConditionsPrice)>0) {
            $sum = array_sum($productConditionsPrice);
            $subtotal = $sum;
        }
        $dividedValue = $helper->getValueExchangeRateDivided($helper->getRealCreditValue($customer));
        if (($dividedValue != $helper->getUsedCreditValue()) || $dividedValue < $subtotal) {
            $creditValue = (float)$helper->getUsedCreditValue();
        } else {
             $creditValue = (float)$subtotal;
             $creditValue += !$shippingAdded ? (float)$shipping : 0;
             $creditValue += !$taxAdded ? (float)$tax : 0;
        }

        $maxCredit = $helper->getMinOrderAmount();
        if ($maxCredit) {
            if ($app->getStore()->isAdmin()) {
                $subtotal += !$taxAdded ? $tax : 0;
                $creditValue = $subtotal*$maxCredit/100;
            } else {
                $creditValue = $subtotal*$maxCredit/100;
            }
        }

        return $creditValue;
    }
}