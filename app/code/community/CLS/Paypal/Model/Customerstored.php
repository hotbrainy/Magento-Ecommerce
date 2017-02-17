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

class CLS_Paypal_Model_Customerstored extends Mage_Core_Model_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->_init('cls_paypal/customerstored');
    }

    /**
     * Return customer's stored card collection
     *
     * @param int $customerId
     * @param string $paymentMethod
     * @return CLS_Paypal_Model_Resource_Customerstored_Collection
     */
    public function getCustomerstoredCollection($customerId, $paymentMethod = null)
    {
        /** @var $collection CLS_Paypal_Model_Resource_Customerstored_Collection */
        $collection = $this->getCollection();
        $collection
            ->filterByCustomerId($customerId)
            ->filterByActiveState()
            ->filterByExpirationDate();

        if (!is_null($paymentMethod)) {
            // Select cards compatible with the current payment method only
            // @todo This section needs to be updated if new payment methods support will be added

            $paymentMethodFilter = array();

            if ($paymentMethod == CLS_Paypal_Model_Paypal_Config::METHOD_PAYPAL_DIRECT_CUSTOMERSTORED) {
                // WPP payment method
                $paymentMethodFilter[] = CLS_Paypal_Model_Paypal_Config::METHOD_WPP_DIRECT;
            }
            else {
                // Payflow methods
                if (Mage::helper('cls_paypal')->isPaymentsAdvancedSupported()) {
                    $paymentMethodFilter[] = CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOWADVANCED;
                }
                $paymentMethodFilter[] = CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOWLINK;
                $paymentMethodFilter[] = CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOWPRO;
            }

            $collection->filterByPaymentMethod($paymentMethodFilter);
        }

        return $collection;
    }

    /**
     * Check if the newly save card already exists
     *
     * @return CLS_Paypal_Model_Customerstored
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getId()) {
            $compatiblePaymentMethods = array();
            
            $compatibleGroups = array(
                array(
                    CLS_Paypal_Model_Paypal_Config::METHOD_WPP_DIRECT,
                ),
                array(
                    CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOWLINK,
                    CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOWPRO,
                )
            );

            if (Mage::helper('cls_paypal')->isPaymentsAdvancedSupported()) {
                array_unshift($compatibleGroups[1], CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOWADVANCED);
            }
            
            foreach ($compatibleGroups as $group) {
                if (in_array($this->getPaymentMethod(), $group)) {
                    $compatiblePaymentMethods = $group;
                    break;
                }
            }
            
            $cardDuplicate = $this->getResource()->checkCardDuplicate(
                $this->getData('customer_id'),
                $this->getData('cc_type'),
                $this->getData('cc_last4'),
                $this->getData('cc_exp_month'),
                $this->getData('cc_exp_year'),
                $compatiblePaymentMethods
            );

            if ($cardDuplicate) {
                // Disallow card save
                $this->_dataSaveAllowed = false;
            }
        }

        return $this;
    }

}
