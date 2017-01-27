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

/**
 * Common "Order stored" class for Payflow methods: Advanced, Link and Pro
 */
class CLS_Paypal_Model_Paypal_Stored_Orderstored_Payflow extends CLS_Paypal_Model_Paypal_Stored_Payflow
{

    protected $_formBlockType = 'cls_paypal/paypal_payment_form_orderstored';

    /**
     * @param array $params
     */
    public function __construct($params = array())
    {
        parent::__construct($params);

        // Initialize common method
        if ( empty($params) || !isset($params['caller_method']) || !isset($params['code']) || !isset($params['parent_code']) ) {
            Mage::throwException(Mage::helper('cls_paypal')->__('Internal error: cannot initialize Payflow payment method.'));
        }

        $this->_commonMethod = Mage::getModel(
            'cls_paypal/paypal_stored_orderstored',
            array('caller_method' => $params['caller_method'])
        );

        $this->_code       = $params['code'];
        $this->_parentCode = $params['parent_code'];
    }

    /**
     * Place an order with authorization or capture action
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float  $amount
     * @param string $transactionType
     * @return CLS_Paypal_Model_Paypal_Stored_Orderstored_Payflow
     */
    protected function _placeOrder(Mage_Sales_Model_Order_Payment $payment, $amount, $transactionType = self::TRXTYPE_AUTH_ONLY)
    {
        // Get Reference ID
        $paymentInfo = $this->_commonMethod->getInfoInstance();
        $referenceId = $paymentInfo->getAdditionalInformation('orderstored_transaction_id');

        if (!$referenceId) {
            Mage::throwException(Mage::helper('cls_paypal')->__('Unable to get the stored card data'));
        }

        // Prepare and run 'Reference Transaction' request
        $request = $this->_buildBasicRequest($payment);
        $request->setTrxtype($transactionType);
        $request->setAmt(round($amount, 2));
        $request->setOrigid($referenceId);

        $response = $this->_postRequest($request);
        $this->_processErrors($response);

        switch ($response->getResultCode()){
            case self::RESPONSE_CODE_APPROVED:
                $payment->setTransactionId($response->getPnref())->setIsTransactionClosed(0);
                break;
            case self::RESPONSE_CODE_FRAUDSERVICE_FILTER:
                $payment->setTransactionId($response->getPnref())->setIsTransactionClosed(0);
                $payment->setIsTransactionPending(true);
                $payment->setIsFraudDetected(true);
                break;
        }
        return $this;
    }

}
