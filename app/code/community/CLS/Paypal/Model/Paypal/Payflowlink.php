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

class CLS_Paypal_Model_Paypal_Payflowlink extends Mage_Paypal_Model_Payflowlink
{
    protected $_formBlockType = 'cls_paypal/paypal_payflow_link_form';

    /**
     * Operate with order using information from silent post
     *
     * @param Mage_Sales_Model_Order $order
     */
    protected function _processOrder(Mage_Sales_Model_Order $order)
    {
        $response = $this->getResponse();
        $payment = $order->getPayment();
        $payment->setTransactionId($response->getPnref())
            ->setIsTransactionClosed(0);
        $canSendNewOrderEmail = true;

        if ($response->getResult() == self::RESPONSE_CODE_FRAUDSERVICE_FILTER ||
            $response->getResult() == self::RESPONSE_CODE_DECLINED_BY_FILTER
        ) {
            $canSendNewOrderEmail = false;
            $fraudMessage = $this->_getFraudMessage() ?
                $response->getFraudMessage() : $response->getRespmsg();
            $payment->setIsTransactionPending(true)
                ->setIsFraudDetected(true)
                ->setAdditionalInformation('paypal_fraud_filters', $fraudMessage);
        }

        if ($response->getAvsdata() && strstr(substr($response->getAvsdata(), 0, 2), 'N')) {
            $payment->setAdditionalInformation('paypal_avs_code', substr($response->getAvsdata(), 0, 2));
        }
        if ($response->getCvv2match() && $response->getCvv2match() != 'Y') {
            $payment->setAdditionalInformation('paypal_cvv2_match', $response->getCvv2match());
        }

        switch ($response->getType()){
            case self::TRXTYPE_AUTH_ONLY:
                $payment->registerAuthorizationNotification($payment->getBaseAmountAuthorized());
                break;
            case self::TRXTYPE_SALE:
                $payment->registerCaptureNotification($payment->getBaseAmountAuthorized());
                break;
        }
        $order->save();

        $customerId = $order->getCustomerId();

        if (
            ($response->getResult() == self::RESPONSE_CODE_APPROVED) &&
            ($response->getMethod() == 'CC') &&
            $customerId &&
            $payment->hasAdditionalInformation('cc_save_future') &&
            ($payment->getAdditionalInformation('cc_save_future') == 'Y')
        ) {
            // Obtain CC type
            $ccType = 'OT';
            $responseCcType = $response->getCardtype();
            if (!is_null($responseCcType)) {
                $payflowResponseCcTypesMap = array(
                    0 => 'VI',
                    1 => 'MC',
                    2 => 'DI',
                    3 => 'AE',
                    4 => 'OT',
                    5 => 'JCB'
                );

                if (isset($payflowResponseCcTypesMap[$responseCcType])) {
                    $ccType = $payflowResponseCcTypesMap[$responseCcType];
                }
            }

            $ccExpMonth = ($response->getExpdate()) ? substr($response->getExpdate(), 0, 2) : '';
            if ($ccExpMonth{0} == '0') {
                $ccExpMonth = $ccExpMonth{1};
            }

            // Create new stored card
            $customerstoredModel = Mage::getModel('cls_paypal/customerstored');
            $customerstoredModel->setData(array(
                'transaction_id' => $response->getPnref(),
                'customer_id'    => $customerId,
                'cc_type' => $ccType,
                'cc_last4' => ($response->getAcct()) ? substr($response->getAcct(), -4) : '',
                'cc_exp_month' => $ccExpMonth,
                'cc_exp_year' => (($response->getExpdate()) ? '20'.substr($response->getExpdate(), 2) : ''),
                'date' => date('Y-m-d H:i:s'),
                'payment_method' => $payment->getMethod()
            ));
            $customerstoredModel->save();
        }

        try {
            if ($canSendNewOrderEmail) {
                $order->sendNewOrderEmail();
            }
            Mage::getModel('sales/quote')
                ->load($order->getQuoteId())
                ->setIsActive(false)
                ->save();
        } catch (Exception $e) {
            Mage::throwException(Mage::helper('paypal')->__('Can not send new order email.'));
        }
    }

}
