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
 * @package    Paypay
 * @copyright  Copyright (c) 2014 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class CLS_Paypal_Helper_Orderstored_Agreement extends Mage_Core_Helper_Data
{
    protected $_sessionOrder = NULL;
    
    /**
     * Check payment additional information for a billing agreement id
     *
     * @return string | bool
     */
    public function getBillingAgreementIdFromSessionOrder()
    {
        $billingAgreementReferenceId = false;
        $order = $this->getSessionOrder();
        if ($order->getId()) {
            //Get payments for this order
            $payments = Mage::getResourceModel('sales/order_payment_collection')
                ->setOrderFilter($order)
                ->addAttributeToSort('entity_id', 'desc');
            foreach ($payments as $payment) {
                if (!$payment->isDeleted()) {
                    //Return the first billing agreement id found
                    if ($paymentReferenceId = $payment->getAdditionalInformation(Mage_Sales_Model_Payment_Method_Billing_AgreementAbstract::PAYMENT_INFO_REFERENCE_ID)) {
                        $billingAgreementReferenceId = $paymentReferenceId;
                        break;
                    }
                }
            }
        }
        return $billingAgreementReferenceId;
    }
    
    /**
     * Get the order from the session. The order or order id is stored differently based on 
     * the current action (edit / new order from this payment)
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSessionOrder() {
        if (is_null($this->_sessionOrder)) {
            //New order from this payment, or re-order
            if (($orderId = Mage::getSingleton('adminhtml/session_quote')->getPreviousOrderId())
                || ($orderId = Mage::getSingleton('adminhtml/session_quote')->getReordered())
            ) {
                $this->_sessionOrder = Mage::getModel('sales/order')->load($orderId);
            } else {
            //Edit
                $this->_sessionOrder = Mage::getSingleton('adminhtml/session_quote')->getOrder();
            }
        }
        return $this->_sessionOrder;
    }
}
