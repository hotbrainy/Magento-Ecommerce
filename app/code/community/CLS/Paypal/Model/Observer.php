<?php
/**
 * Classy Llama
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email to us at
 * support+paypal@classyllama.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future. If you require customizations of this
 * module for your needs, please write us at sales@classyllama.com.
 *
 * To report bugs or issues with this module, please email support+paypal@classyllama.com.
 * 
 * @category   CLS
 * @package    Paypal
 * @copyright  Copyright (c) 2014 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class CLS_Paypal_Model_Observer
{

    /**
     * Catch guest order creation in admin
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function adminCheckGuestOrder(Varien_Event_Observer $observer)
    {
        /* @var $request Mage_Core_Controller_Request_Http */
        $request = Mage::app()->getRequest();

        /** @var $session Mage_Adminhtml_Model_Session_Quote */
        $session = Mage::getSingleton('adminhtml/session_quote');

        if ($request->getParam('is_guest_order')) {
            // In case of guest order mark the quote appropriately
            $session->getQuote()->setCustomerIsGuest(true);

            if ( ($originalOrderId = $session->getPreviousOrderId()) && (!$session->getIsGuestAddressPopulated()) ) {
                // Populate address fields from the original order
                $originalOrder = Mage::getModel('sales/order')->load($originalOrderId);

                if ($originalOrder->getId()) {
                    $session->getQuote()->getBillingAddress()->setCustomerAddressId('');
                    Mage::helper('core')->copyFieldset(
                        'sales_copy_order_billing_address',
                        'to_order',
                        $originalOrder->getBillingAddress(),
                        $session->getQuote()->getBillingAddress()
                    );

                    $session->getQuote()->getShippingAddress()->setCustomerAddressId('');
                    Mage::helper('core')->copyFieldset(
                        'sales_copy_order_shipping_address',
                        'to_order',
                        $originalOrder->getShippingAddress(),
                        $session->getQuote()->getShippingAddress()
                    );

                    // Don't set our "guest address populated" flag on the session just yet,
                    // because quote isn't saved during this request in every case.  Wait for save.
                    $session->getQuote()->setFlagGuestAddressOnSave(true);
                }
            }
        }
    }

    /**
     * On quote save during admin order creation, if guess address was populated in this request,
     * set a flag on the session
     *
     * @param Varien_Event_Observer $observer
     */
    public function adminQuoteFlagGuestAddress(Varien_Event_Observer $observer)
    {
        $quote = $observer->getQuote();
        if ($quote->getFlagGuestAddressOnSave()) {
            Mage::getSingleton('adminhtml/session_quote')->setIsGuestAddressPopulated(true);
        }
    }

    /**
     * Handle custom payment data fields
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function salesQuotePaymentImportDataBefore(Varien_Event_Observer $observer)
    {
        /** @var $input Varien_Object */
        $input = $observer->getInput();

        /** @var $payment Mage_Sales_Model_Quote_Payment */
        $payment = $observer->getPayment();

        $paymentMethod = $input->getMethod();

        if (isset($paymentMethod)) {
            // Clear any possible additional info from previous requests
            $customPaymentAddInfoFields = CLS_Paypal_Helper_Data::$customPaymentAddInfoFields;
            if (!empty($customPaymentAddInfoFields)) {
                foreach ($customPaymentAddInfoFields as $_customField) {
                    if ($payment->hasAdditionalInformation($_customField)) {
                        $payment->unsAdditionalInformation($_customField);
                    }
                }
            }

            /** @var $helper CLS_Paypal_Helper_Data */
            $helper = Mage::helper('cls_paypal');

            // Handle "Save this card" option for native Magento payment methods
            if ($helper->isSupportedPaymentMethod($paymentMethod)) {
                if ($input['cc_save_future'] == 'Y') {
                    // Set 'Save for future use' flag
                    $payment->setAdditionalInformation('cc_save_future', 'Y');
                }
            }

            // Handle "Customer stored card" payment methods selection
            elseif ($helper->isCustomerstoredPaymentMethod($paymentMethod)) {
                if ($input['stored_card_id']) {
                    // Save stared card ID into the additional information,
                    // card ID will be validated afterwards
                    $payment->setAdditionalInformation('stored_card_id', $input['stored_card_id']);
                }
            }
        }
    }

    /**
     * Add custom data to the payment info block
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function paymentInfoBlockPrepareSpecificInformation(Varien_Event_Observer $observer)
    {
        /** @var $payment Mage_Sales_Model_Quote_Payment */
        $payment = $observer->getPayment();

        /** @var $transport Varien_Object */
        $transport = $observer->getTransport();

        $paymentMethod = $payment->getMethod();

        // Display custom data for supported payment methods only
        /** @var $helper CLS_Paypal_Helper_Data */
        $helper = Mage::helper('cls_paypal');

        if ($helper->isSupportedPaymentMethod($paymentMethod)) {
            if ($payment->hasAdditionalInformation('cc_save_future')) {
                $transport->setData($helper->__('Save this card for future use'), $helper->__('Yes'));
            }
        }
    }

    /**
     * Save stored card details
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function checkoutSubmitAllAfter(Varien_Event_Observer $observer)
    {
        $orders = $observer->getOrders();
        if (is_null($orders)) {
            $orders = array($observer->getOrder());
        }

        foreach($orders as $order) {
            /** @var $order Mage_Sales_Model_Order */

            $customerId = $order->getData('customer_id');

            if (!$customerId) {
                // Save transaction details for registered customers only
                return;
            }

            $payment = $order->getPayment();
            $paymentMethod = $payment->getMethod();

            if ( isset($paymentMethod) && $payment->getData('transaction_id') ) {

                /** @var $helper CLS_Paypal_Helper_Data */
                $helper = Mage::helper('cls_paypal');

                if ($helper->isSdkPaymentMethod($paymentMethod)) {
                    if (
                        $payment->hasAdditionalInformation('cc_save_future') &&
                        ($payment->getAdditionalInformation('cc_save_future') == 'Y')
                    ) {
                        // Create new stored card
                        $customerstoredModel = Mage::getModel('cls_paypal/customerstored');
                        $customerstoredModel->setData(array(
                            'transaction_id' => $payment->getData('transaction_id'),
                            'customer_id'    => $customerId,
                            'cc_type' => $payment->getData('cc_type'),
                            'cc_last4' => $payment->getData('cc_last4'),
                            'cc_exp_month' => $payment->getData('cc_exp_month'),
                            'cc_exp_year' => $payment->getData('cc_exp_year'),
                            'date' => date('Y-m-d H:i:s'),
                            'payment_method' => $paymentMethod
                        ));
                        $customerstoredModel->save();
                    }
                }

                elseif ($helper->isCustomerstoredPaymentMethod($paymentMethod)) {
                    // Update existing stored card
                    $customerstoredModel = Mage::getModel('cls_paypal/customerstored');

                    if ($payment->hasAdditionalInformation('stored_card_id')) {
                        // Reference payment using existing customer's stored card
                        $storedCardId = $payment->getAdditionalInformation('stored_card_id');
                        $customerstoredModel->load($storedCardId);

                        if ($customerstoredModel->getId()) {
                            // Update stored card record with a new transaction ID
                            $customerstoredModel
                                ->setData('transaction_id', $payment->getData('transaction_id'))
                                ->setData('date', date('Y-m-d H:i:s'));
                            $customerstoredModel->save();
                        }
                    }
                }
            }
        }
    }

}
