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

class CLS_Paypal_Model_Paypaluk_Express_Checkout extends Mage_PaypalUk_Model_Express_Checkout
{
    /**
     * Set create billing agreement flag to api call
     *
     * @return Mage_Paypal_Model_Express_Checkout
     */
    protected function _setBillingAgreementRequest()
    {
        if ($this->_quote->hasNominalItems()) {
            return $this;
        }

        $isRequested = $this->_isBARequested || $this->_quote->getPayment()
            ->getAdditionalInformation(self::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);

        if (!($this->_config->allow_ba_signup == Mage_Paypal_Model_Config::EC_BA_SIGNUP_AUTO
            || $isRequested && $this->_config->shouldAskToCreateBillingAgreement())) {
            return $this;
        }

        if ($this->_customerId && !Mage::getModel('sales/billing_agreement')->needToCreateForCustomer($this->_customerId)) {
            return $this;
        }
        $this->_api->setBillingType($this->_api->getBillingAgreementType());
        return $this;
    }
}
