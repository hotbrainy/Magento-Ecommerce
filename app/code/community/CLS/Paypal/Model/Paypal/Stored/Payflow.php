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

class CLS_Paypal_Model_Paypal_Stored_Payflow extends CLS_Paypal_Model_Paypal_Payflowpro
{

    /**
     * Payment method code
     */
    protected $_code = null;

    /**
     * Keeps the payment method that handles all common 'stored' functionality
     *
     * @var CLS_Paypal_Model_Paypal_Stored_Abstract
     */
    protected $_commonMethod;

    /**
     * Parent payment method code
     */
    protected $_parentCode = null;

    /**
     * The list of config fields that should be taken from the actual 'stored' payment method
     * @var array|null
     */
    protected $_actualConfigFields = array(
        'active',
        'title',
        'sort_order',
        'payment_action',
        'allowspecific',
        'specificcountry',
        'debug',
        'verify_peer'
    );

    /**
     * Check whether payment method can be used
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        $storeId = Mage::app()->getStore($this->getStore())->getId();
        $config = Mage::getModel('paypal/config')->setStoreId($storeId);

        if (!(
            Mage_Payment_Model_Method_Abstract::isAvailable($quote) &&
                $config->isMethodAvailable($this->getCode()))
        ) {
            return false;
        }

        return $this->_commonMethod->isAvailable($quote);
    }

    /**
     * Validate payment method information object
     *
     * @return Mage_Payment_Model_Method_Cc
     */
    public function validate()
    {
        return $this->_commonMethod->validate();
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Method_Cc
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
     * @return CLS_Paypal_Model_Paypal_Stored_Payflow
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        return $this->_placeOrder($payment, $amount, self::TRXTYPE_AUTH_ONLY);
    }

    /**
     * Capture payment via reference transaction
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return CLS_Paypal_Model_Paypal_Stored_Payflow
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if ($payment->getParentTransactionId()) {
            // Perform 'Delayed capture' in a usual way
            return parent::capture($payment, $amount);
        }
        else {
            // Capture funds via Reference Transaction
            return $this->_placeOrder($payment, $amount, self::TRXTYPE_SALE);
        }
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int|string|null|Mage_Core_Model_Store $storeId
     *
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }

        if (!empty($this->_actualConfigFields) && in_array($field, $this->_actualConfigFields)) {
            // Get this field value from the actual payment method
            $code = $this->getCode();
        }
        else {
            // Get the rest values from the parent method
            $code = $this->getParentCode();
        }

        $path = 'payment/'.$code.'/'.$field;

        return Mage::getStoreConfig($path, $storeId);
    }

    /**
     * Retrieve parent payment method code
     *
     * @return string
     */
    public function getParentCode()
    {
        if (!is_null($this->_parentCode)) {
            return $this->_parentCode;
        }

        return $this->getCode();
    }

}
