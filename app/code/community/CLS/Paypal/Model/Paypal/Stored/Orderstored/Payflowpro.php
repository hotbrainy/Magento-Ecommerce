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

class CLS_Paypal_Model_Paypal_Stored_Orderstored_Payflowpro extends CLS_Paypal_Model_Paypal_Payflowpro
{

    /**
     * Payment method code
     */
    protected $_code = CLS_Paypal_Model_Paypal_Config::METHOD_PAYPAL_PAYFLOWPRO_ORDERSTORED;

    protected $_formBlockType = 'cls_paypal/paypal_payment_form_orderstored';

    /**
     * Common payment method for the Payflow family
     *
     * @var CLS_Paypal_Model_Paypal_Stored_Orderstored_Payflow
     */
    protected $_commonPayflowMethod;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        // Initialize common method
        $this->_commonPayflowMethod = Mage::getModel(
            'cls_paypal/paypal_stored_orderstored_payflow',
            array(
                'caller_method' => $this,
                'code'          => $this->_code,
                'parent_code'   => CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOWPRO
            )
        );
    }

    /**
     * Set the info instance on the common method too
     *
     * @param Mage_Payment_Model_Info $info
     */
    public function setInfoInstance($info)
    {
        $this->_commonPayflowMethod->setInfoInstance($info);
        $this->setData('info_instance', $info);
    }

    /**
     * Check whether payment method can be used
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return $this->_commonPayflowMethod->isAvailable($quote);
    }

    /**
     * Validate payment method information object
     *
     * @return CLS_Paypal_Model_Paypal_Stored_Orderstored_Payflowpro
     */
    public function validate()
    {
        return $this->_commonPayflowMethod->validate();
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  CLS_Paypal_Model_Paypal_Stored_Orderstored_Payflowpro
     */
    public function assignData($data)
    {
        return $this->_commonPayflowMethod->assignData($data);
    }

    /**
     * Get payment action
     *
     * @see Mage_Sales_Model_Payment::place()
     * @return string
     */
    public function getConfigPaymentAction()
    {
        return $this->_commonPayflowMethod->getConfigPaymentAction();
    }

    /**
     * Authorize payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return CLS_Paypal_Model_Paypal_Stored_Orderstored_Payflowpro
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        return $this->_commonPayflowMethod->authorize($payment, $amount);
    }

    /**
     * Capture payment
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return CLS_Paypal_Model_Paypal_Stored_Orderstored_Payflowpro
     */
    public function capture(Varien_Object $payment, $amount)
    {
        return $this->_commonPayflowMethod->capture($payment, $amount);
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
        return $this->_commonPayflowMethod->getConfigData($field, $storeId);
    }

    /**
     * Set the store on the model instance and common method
     *
     * @param int $store
     * @return $this
     */
    public function setStore($store) {
        $this->_commonPayflowMethod->setStore($store);
        $this->setData('store', $store);
        return $this;
    }
}
