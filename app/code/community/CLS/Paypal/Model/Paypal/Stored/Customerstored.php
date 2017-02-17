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
 * Contains common logic for the "Customer stored" methods family
 */
class CLS_Paypal_Model_Paypal_Stored_Customerstored extends CLS_Paypal_Model_Paypal_Stored_Abstract
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
        $customerId = false;
        
        // Disallow method for guest orders
        if (!is_null($quote)) {
            $customerId = $quote->getCustomerId();
        } else {
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        }
        
        if (!$customerId
            || (!Mage::getSingleton('customer/session')->isLoggedIn() && !Mage::app()->getStore()->isAdmin())) {
            return false;
        }

        // Check if active saved cards exist for the customer
        $this->_callerMethod->setCustomerId($customerId);

        $storedCards = $this->getStoredCards();
        if ( $storedCards && ($storedCards->count() > 0) ) {
            return true;
        }

        return false;
    }

    /**
     * Return the list of customer's stored cards
     *
     * @return CLS_Paypal_Model_Resource_Customerstored_Collection|null
     */
    public function getStoredCards()
    {
        if (is_null($this->_callerMethod->getData('stored_cards'))) {
            $customerId = $this->_callerMethod->getCustomerId();

            if (!$customerId) {
                return null;
            }

            /** @var $collection CLS_Paypal_Model_Resource_Customerstored_Collection */
            $collection = Mage::getSingleton('cls_paypal/customerstored')->getCustomerstoredCollection($customerId, $this->_callerMethod->getCode());
            $this->_callerMethod->setData('stored_cards', $collection);
        }

        return $this->_callerMethod->getData('stored_cards');
    }

    /**
     * Validate payment method information object
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function validate()
    {
        parent::validate();

        $paymentInfo = $this->getInfoInstance();
        $paymentStoredCardId = $paymentInfo->getAdditionalInformation('stored_card_id');

        $isError = true;

        // Check if customer ID is available
        if (!$this->_callerMethod->getCustomerId()) {
            if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
                $customerId = $paymentInfo->getOrder()->getCustomerId();
            } else {
                $customerId = $paymentInfo->getQuote()->getCustomerId();
            }

            $this->_callerMethod->setCustomerId($customerId);
        }

        // Check stored card ID for validity
        if ($paymentStoredCardId) {
            $storedCards = $this->getStoredCards();

            if ( $storedCards && ($storedCards->count() > 0) ) {
                foreach ($storedCards as $storedCard) {
                    if ($storedCard->getStoredCardId() == $paymentStoredCardId) {
                        // Also keep stored card transaction ID
                        $paymentInfo->setAdditionalInformation('stored_card_transaction_id', $storedCard->getTransactionId());

                        $isError = false;
                        break;
                    }
                }
            }
        }

        if ($isError) {
            Mage::throwException(Mage::helper('cls_paypal')->__('Please select valid saved card'));
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

        // Assign actual payment method data
        $storedCardId = $data->getStoredCardId();
        $storedCards = $this->getStoredCards();

        $info->setStoredCardId($storedCardId);

        // Make sure no extraneous data is kept
        $info->setCcType(null)
            ->setCcOwner(null)
            ->setCcLast4(null)
            ->setCcNumber(null)
            ->setCcCid(null)
            ->setCcExpMonth(null)
            ->setCcExpYear(null)
            ->setCcSsIssue(null)
            ->setCcSsStartMonth(null)
            ->setCcSsStartYear(null);

        if ( !is_null($storedCardId) && !is_null($storedCards) ) {
            foreach ($storedCards as $storedCard) {
                if ($storedCardId == $storedCard->getId()) {
                    // Assign CC info (taken from the selected stored card)
                    $info
                        ->setCcType($storedCard->getCcType())
                        ->setCcLast4($storedCard->getCcLast4())
                        ->setCcExpMonth($storedCard->getCcExpMonth())
                        ->setCcExpYear($storedCard->getCcExpYear());

                    break;
                }
            }
        }


        return $this->_callerMethod;
    }

}
