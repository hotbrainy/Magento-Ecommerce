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

class CLS_Paypal_Model_Paypaluk_Method_Orderstored_Agreement extends CLS_Paypal_Model_Paypaluk_Method_Agreement
{

    /**
     * Method code
     *
     * @var string
     */
    protected $_code = CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOW_ORDERSTORED_AGREEMENT;
    protected $_formBlockType = 'cls_paypal/paypaluk_payment_form_orderstored_agreement';

    /**
     * Method instance settings
     *
     */
    protected $_canUseInternal = true;

    /**
     * Check whether method is available
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (is_null($this->_isAvailable)) {
            $this->_isAvailable = (Mage_Payment_Model_Method_Abstract::isAvailable($quote) && $this->_isAvailable($quote));
            
            if ($this->_isAvailable && is_object($quote)) {
                $isAvailable = false;
                $availableBA = Mage::helper('cls_paypal/orderstored_agreement')->getBillingAgreementIdFromSessionOrder();
                $isAvailable = !empty($availableBA);
                $this->_isAvailable = $isAvailable;
            }
            $this->_canUseInternal = ($this->_isAvailable && $this->_canUseInternal);
        }
        return $this->_isAvailable;
    }

    /**
     * Place an order with authorization or capture action
     *
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param float $amount
     * @return Mage_Paypal_Model_Method_Agreement
     */
    protected function _placeOrder(Mage_Sales_Model_Order_Payment $payment, $amount)
    {
        $order = $payment->getOrder();

        $api = $this->_pro->getApi()
            ->setBillingAgreementId($payment->getBillingAgreementId())
            ->setPaymentAction($this->_pro->getConfig()->paymentAction)
            ->setAmount($amount)
            ->setNotifyUrl(Mage::getUrl('paypal/ipn/'))
            ->setPaypalCart(Mage::getModel('paypal/cart', array($order)))
            ->setIsLineItemsEnabled($this->_pro->getConfig()->lineItemsEnabled)
            ->setInvNum($order->getIncrementId())
        ;

        // call api and import transaction and other payment information
        $api->callDoReferenceTransaction();
        $this->_pro->importPaymentInfo($api, $payment);

        $payment
            ->setAdditionalInformation(CLS_Paypal_Model_Paypaluk_Api_Nvp_Common::RESPONSE_MSG, $api->getResponseMsg())
            ->setPreparedMessage(Mage::helper('cls_paypal')->__('Payflow PNREF: #%s.', $api->getTransactionId()))
            ->setTransactionAdditionalInfo(Mage_PaypalUk_Model_Pro::TRANSPORT_PAYFLOW_TXN_ID, $api->getTransactionId());

        $payment->setTransactionId($api->getPaypalTransactionId())
            ->setIsTransactionClosed(0);

        return $this;
    }
}
