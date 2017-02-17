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

class CLS_Paypal_Model_Paypal_Stored_Orderstored extends CLS_Paypal_Model_Paypal_Stored_Abstract
{
    /**
     * Check whether payment method can be used
     * (we consider the caller payment availability has been already checked before)
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        // Allow method for admin only
        if (!Mage::app()->getStore()->isAdmin()) {
            return false;
        }

        if ($this->_getOriginalOrderData()) {
            return true;
        }

        return false;
    }

    /**
     * Validate payment method information object
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function validate()
    {
        parent::validate();

        if (!$originalOrderData = $this->_getOriginalOrderData()) {
            Mage::throwException(Mage::helper('cls_paypal')->__('Unable to get the transaction data'));
        }
        else {
            // Keep payment-specific data
            $paymentInfo = $this->getInfoInstance();

            $paymentInfo->setAdditionalInformation('orderstored_original_order_id', $originalOrderData['order_id']);
            $paymentInfo->setAdditionalInformation('orderstored_transaction_id', $originalOrderData['transaction_id']);
        }

        return $this->_callerMethod;
    }

    /**
     * Assign data to info model instance
     *
     * @param   mixed $data
     * @return  Mage_Payment_Model_Method_Abstract
     */
    public function assignData($data)
    {
        if (!($data instanceof Varien_Object)) {
            $data = new Varien_Object($data);
        }
        $info = $this->getInfoInstance();

        // Make sure no extraneous data is kept
        $info
            ->setStoredCardId(null)
            ->setCcType(null)
            ->setCcOwner(null)
            ->setCcLast4(null)
            ->setCcNumber(null)
            ->setCcCid(null)
            ->setCcExpMonth(null)
            ->setCcExpYear(null)
            ->setCcSsIssue(null)
            ->setCcSsStartMonth(null)
            ->setCcSsStartYear(null);

        /** @var $session Mage_Adminhtml_Model_Session_Quote */
        $session = Mage::getSingleton('adminhtml/session_quote');

        if (
            ($originalOrderId = $session->getPreviousOrderId())
            || ($originalOrderId = $session->getReordered())
            || ($originalOrderId = $session->getOrderId())
        ) {
            // Assign CC info (taken from the original order)
            $originalOrder = Mage::getModel('sales/order')->load($originalOrderId);

            if ($originalOrder->getId()) {
                $originalOrderPayment = $originalOrder->getPayment();

                if ($originalOrderPayment->getId()) {
                    $info
                        ->setCcType($originalOrderPayment->getCcType())
                        ->setCcLast4($originalOrderPayment->getCcLast4())
                        ->setCcExpMonth($originalOrderPayment->getCcExpMonth())
                        ->setCcExpYear($originalOrderPayment->getCcExpYear());
                }
            }
        }

        return $this->_callerMethod;
    }

    /**
     * Check original order and return specific data (order ID, transaction ID)
     *
     * @return  array|null
     */
    protected function _getOriginalOrderData()
    {
        // Check original order
        /** @var $session Mage_Adminhtml_Model_Session_Quote */
        $session = Mage::getSingleton('adminhtml/session_quote');

        if (
            ($originalOrderId = $session->getPreviousOrderId())
            || ($originalOrderId = $session->getOrderId())
            || ($originalOrderId = $session->getReordered())
        ) {
            // Get original order data
            /** @var $originalOrder Mage_Sales_Model_Order */
            $originalOrder = Mage::getModel('sales/order')->load($originalOrderId);

            if ($originalOrder->getId()) {
                $originalOrderPayment = $originalOrder->getPayment();

                /** @var $helper CLS_Paypal_Helper_Data */
                $helper = Mage::helper('cls_paypal');

                // Check payment method for compatibility
                if (
                    $originalOrderPayment->getId()
                    && ($helper->isSupportedPaymentMethodFull($originalOrderPayment->getMethod()))
                    && ($helper->isValidParentPaymentMethod($this->_callerMethod->getCode(), $originalOrderPayment->getMethod()))
                ) {
                    // Select the last valid order's transaction
                    /** @var $transactionCollection Mage_Sales_Model_Resource_Order_Payment_Transaction_Collection */
                    $transactionCollection = Mage::getResourceModel('sales/order_payment_transaction_collection');

                    $pastDate = new Zend_Date(null);
                    $pastDate->addMonth(0 - CLS_Paypal_Model_Paypal_Config::STORED_CARD_TTL_MONTHS);

                    $transactionCollection
                        ->addOrderIdFilter($originalOrder->getId())
                        ->addPaymentIdFilter($originalOrderPayment->getId())
                        ->addAttributeToFilter('main_table.created_at', array('gt' => Mage::getModel('core/date')->gmtDate(null, $pastDate->toValue())))
                        ->setOrder('main_table.created_at', Varien_Data_Collection::SORT_ORDER_DESC);

                    $transaction = false;
                    foreach ($transactionCollection as $trans) {
                        if ($trans->getTxnType() !== Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND
                            && $trans->getTxnType() !== Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID) {
                            $transaction = $trans;
                            break;
                        }
                    }

                    if ($transaction && !Mage::helper('cls_paypal')->transactionIsVoided($transaction)) {
                        return array(
                            'order_id'       => $originalOrderId,
                            'transaction_id' => $transaction->getTxnId()
                        );
                    }
                }
            }
        }

        return null;
    }

}
