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

class CLS_Paypal_Helper_Data extends Mage_Core_Helper_Abstract
{

    const DEFAULT_PAYPAL_INFO_CODE = 'Q2xhc3N5TGxhbWFfU0lfTWFnZW50b0VFX1JlZlRyYW4=';
    const CONFIG_PATH_PAYPAL_INFO_CODE = 'cls_paypal/general/info_code';

    /**
     * Native payment methods supported by the module
     *
     * @var array
     */
    public static $supportedPaymentMethods = array(
        CLS_Paypal_Model_Paypal_Config::METHOD_WPP_DIRECT,
        CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOWLINK,
        CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOWPRO
    );

    /**
     * Module-specific custom payment methods
     *
     * @var array
     */
    public static $customPaymentMethods = array(
        CLS_Paypal_Model_Paypal_Config::METHOD_PAYPAL_DIRECT_CUSTOMERSTORED,
        CLS_Paypal_Model_Paypal_Config::METHOD_PAYPAL_PAYFLOWADVANCED_CUSTOMERSTORED,
        CLS_Paypal_Model_Paypal_Config::METHOD_PAYPAL_PAYFLOWPRO_CUSTOMERSTORED,
        CLS_Paypal_Model_Paypal_Config::METHOD_PAYPAL_PAYFLOWLINK_CUSTOMERSTORED,
        CLS_Paypal_Model_Paypal_Config::METHOD_PAYPAL_DIRECT_ORDERSTORED,
        CLS_Paypal_Model_Paypal_Config::METHOD_PAYPAL_PAYFLOWADVANCED_ORDERSTORED,
        CLS_Paypal_Model_Paypal_Config::METHOD_PAYPAL_PAYFLOWPRO_ORDERSTORED,
        CLS_Paypal_Model_Paypal_Config::METHOD_PAYPAL_PAYFLOWLINK_ORDERSTORED
    );

    /**
     * SDK-based payment methods
     *
     * @var array
     */
    public static $sdkPaymentMethods = array(
        CLS_Paypal_Model_Paypal_Config::METHOD_WPP_DIRECT,
        CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOWPRO
    );

    /**
     * Custom Payment additional information fields
     *
     * @var array
     */
    public static $customPaymentAddInfoFields = array(
        'cc_save_future',
        'stored_card_id',
        'stored_card_transaction_id',
        'orderstored_original_order_id',
        'orderstored_transaction_id'
    );

    /**
     * Helper constructor
     *
     * @return void
     */
    public function __construct() {
        //Include Payments Advanced if supported
        if ($this->isPaymentsAdvancedSupported()) {
            self::$supportedPaymentMethods[] = CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOWADVANCED;
        }
    }

    /**
     * Checks if Paypal Payments Advanced is supported
     *
     * @return bool
     */
    public function isPaymentsAdvancedSupported()
    {
        return @class_exists('Mage_Paypal_Model_Payflowadvanced');
    }

    /**
     * Return parameters for the "New Order from this Payment" button (admin)
     *
     * @return string
     */
    public function getAdminOrderFromPaymentButton()
    {
        $params = array();

        if ( $order = Mage::registry('sales_order') ) {
            $params['order_id'] = $order->getId();
        }

        return array(
            'label'   => $this->__('New Order from this Payment'),
            'onclick' => 'setLocation(\''.Mage::helper("adminhtml")->getUrl('*/sales_order_create/orderFromPayment', $params).'\')'
        );
    }

    /**
     * Get Paypal info code
     *
     * @return string
     */
    public function getPaypalInfoCode() {
        $code = Mage::getStoreConfig(self::CONFIG_PATH_PAYPAL_INFO_CODE);
        if (!$code) {
            $code = self::DEFAULT_PAYPAL_INFO_CODE;
        }
        return base64_decode($code);
    }

    /**
     * Check if any of supported payment methods is active
     *
     * @todo Remove this method if it will not be actually used
     * @return bool
     */
    public function isSupportedCcMethodEnabled()
    {
        if (!empty(self::$supportedPaymentMethods)) {
            foreach (self::$supportedPaymentMethods as $paymentMethodCode) {
                if ( (bool)Mage::getStoreConfig("payment/{$paymentMethodCode}/active") === true ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check whether the payment method is supported
     *
     * @param string $paymentMethod
     * @return bool
     */
    public function isSupportedPaymentMethod($paymentMethod)
    {
        if (
            $paymentMethod &&
            !empty(self::$supportedPaymentMethods) &&
            in_array($paymentMethod, self::$supportedPaymentMethods)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check among native and custom methods whether the payment method is supported
     *
     * @param string $paymentMethod
     * @return bool
     */
    public function isSupportedPaymentMethodFull($paymentMethod)
    {
        // Check native methods
        if ($this->isSupportedPaymentMethod($paymentMethod)) {
            return true;
        }

        // Check custom methods
        if (
            $paymentMethod &&
            !empty(self::$customPaymentMethods) &&
            in_array($paymentMethod, self::$customPaymentMethods)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check whether the payment method is based on PayPal SDK
     *
     * @param string $paymentMethod
     * @return bool
     */
    public function isSdkPaymentMethod($paymentMethod)
    {
        if (
            $paymentMethod &&
            !empty(self::$sdkPaymentMethods) &&
            in_array($paymentMethod, self::$sdkPaymentMethods)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if the payment method belongs to the 'Customer Stored' family
     *
     * @param string $paymentMethod
     * @return bool
     */
    public function isCustomerstoredPaymentMethod($paymentMethod)
    {
        if ( $paymentMethod && (strpos($paymentMethod, '_customerstored') !== false) ) {
            return true;
        }

        return false;
    }

    /**
     * Check if the payment method is compatible with its parent method
     *
     * @param string $paymentMethod
     * @param string $parentPaymentMethod
     * @return bool
     */
    public function isValidParentPaymentMethod($paymentMethod, $parentPaymentMethod)
    {
        // WPP-based methods
        if (
            (strpos($paymentMethod, 'paypal_direct') !== false)
            && (strpos($parentPaymentMethod, 'paypal_direct') !== false)
        ) {
            return true;
        }

        // Payflow-based methods
        if (
            (strpos($paymentMethod, 'payflow_') !== false || strpos($paymentMethod, 'verisign') !== false)
            && (strpos($parentPaymentMethod, 'payflow_') !== false || strpos($parentPaymentMethod, 'verisign') !== false)
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if the "Save this card" feature is allowed
     * (for any not-guest order, child customerstored method should be active)
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param string $paymentMethodCode
     * @return bool
     */
    public function isCcSaveAllowed($quote, $paymentMethodCode)
    {
        if (!$paymentMethodCode) {
            return false;
        }
        else {
            // @todo Adjust this code if 'customerstored' methods naming convention will change
            $customerstoredPaymentMethodCode = $paymentMethodCode . '_customerstored';
        }

        if (
            $quote &&
            !(
                $quote->getData('checkout_method') == Mage_Checkout_Model_Type_Onepage::METHOD_GUEST ||
                $quote->getData('customer_is_guest')
            ) &&
            Mage::getStoreConfig("payment/{$customerstoredPaymentMethodCode}/active", $quote->getStoreId())
        ) {
            return true;
        }

        return false;
    }

    /**
     * Determine if a transaction has been voided, using the "txn_type" field
     * instead of "type" like the native isVoided method does
     *
     * @param Mage_Sales_Model_Order_Payment_Transaction $trans
     * @return bool
     */
    public function transactionIsVoided($trans)
    {
        $children = $trans->getChildTransactions();

        $result = false;
        if (Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH === $trans->getTxnType()) {
            foreach ($children as $child) {
                if (Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID === $child->getTxnType()) {
                    $result = true;
                }
            }
        }

        return $result;
    }
}
