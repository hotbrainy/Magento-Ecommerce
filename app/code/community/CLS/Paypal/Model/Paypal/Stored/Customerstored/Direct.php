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

class CLS_Paypal_Model_Paypal_Stored_Customerstored_Direct extends CLS_Paypal_Model_Paypal_Direct
{

    protected $_code  = CLS_Paypal_Model_Paypal_Config::METHOD_PAYPAL_DIRECT_CUSTOMERSTORED;
    protected $_formBlockType = 'cls_paypal/paypal_payment_form_customerstored';

    /**
     * Keeps the payment method that handles all common 'Customer Stored' functionality
     *
     * @var CLS_Paypal_Model_Paypal_Stored_Customerstored
     */
    protected $_commonMethod;

    /**
     * @param array $params
     */
    public function __construct($params = array())
    {
        parent::__construct($params);

        // Set fallback method
        $this->_pro->getConfig()->setFallBackMethod(Mage_Paypal_Model_Config::METHOD_WPP_DIRECT);

        // Initialize common method
        $this->_commonMethod = Mage::getModel('cls_paypal/paypal_stored_customerstored', array('caller_method' => $this));
    }

    /**
     * Check whether payment method can be used
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (!(
            Mage_Payment_Model_Method_Abstract::isAvailable($quote) &&
            $this->_pro->getConfig()->isMethodAvailable())
        ) {
            return false;
        }

        return $this->_commonMethod->isAvailable($quote);
    }

    /**
     * Validate payment method information object
     *
     * @return CLS_Paypal_Model_Paypal_Stored_Customerstored_Direct
     */
    public function validate()
    {
        return $this->_commonMethod->validate();
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  CLS_Paypal_Model_Paypal_Stored_Customerstored_Direct
     */
    public function assignData($data)
    {
        return $this->_commonMethod->assignData($data);
    }

    /**
     * Authorize payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return CLS_Paypal_Model_Paypal_Stored_Customerstored_Direct
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        return $this->_placeOrder($payment, $amount);
    }

    /**
     * Place an order with authorization or capture action
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return CLS_Paypal_Model_Paypal_Stored_Customerstored_Direct
     */
    protected function _placeOrder(Mage_Sales_Model_Order_Payment $payment, $amount)
    {
        // Get Reference ID
        $paymentStoredCardId = $this->getInfoInstance()->getData('stored_card_id');
        if (is_null($paymentStoredCardId)) {
            $paymentStoredCardId = $this->getInfoInstance()->getAdditionalInformation('stored_card_id');
        }
        $referenceId = null;

        if ($paymentStoredCardId) {
            $storedCardModel = Mage::getModel('cls_paypal/customerstored')->load($paymentStoredCardId);
            if ($storedCardModel->getId()) {
                $referenceId = $storedCardModel->getData('transaction_id');
            }
        }

        if (!$referenceId) {
            Mage::throwException(Mage::helper('cls_paypal')->__('Unable to get the stored card data'));
        }

        $order = $payment->getOrder();

        $api = $this->_pro->getApi()
            ->setReferenceId($referenceId)
            ->setPaymentAction($this->getConfigData('payment_action'))
            ->setIpAddress(Mage::app()->getRequest()->getClientIp(false))

            ->setAmount($amount)
            ->setCurrencyCode($order->getBaseCurrencyCode())
            ->setInvNum($order->getIncrementId())
            ->setEmail($order->getCustomerEmail());

        // add shipping and billing addresses
        /*
        if ($order->getIsVirtual()) {
            $api->setAddress($order->getBillingAddress())->setSuppressShipping(true);
        } else {
            $api->setAddress($order->getShippingAddress());
            $api->setBillingAddress($order->getBillingAddress());
        }
        */

        // add line items
        $api->setPaypalCart(Mage::getModel('paypal/cart', array($order)))
            ->setIsLineItemsEnabled($this->_pro->getConfig()->lineItemsEnabled)
        ;

        // call api and import transaction and other payment information
        $api->callDoReferenceTransaction();
        $this->_importResultToPayment($api, $payment);

        try {
            $api->callGetTransactionDetails();
        } catch (Mage_Core_Exception $e) {
            // if we recieve errors, but DoDirectPayment response is Success, then set Pending status for transaction
            $payment->setIsTransactionPending(true);
        }
        $this->_importResultToPayment($api, $payment);
        return $this;
    }

}
