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

require_once Mage::getModuleDir('controllers', 'Mage_PaypalUk') . DS . 'ExpressController.php';

/**
 * Class CLS_Paypal_PaypalUk_ExpressController
 */
class CLS_Paypal_PaypalUk_ExpressController extends Mage_PaypalUk_ExpressController
{

    /**
     * Start Express Checkout by requesting initial token and dispatching customer to PayPal
     */
    public function startAction()
    {
        try {
            $this->_initCheckout();

            if ($this->_getQuote()->getIsMultiShipping()) {
                $this->_getQuote()->setIsMultiShipping(false);
                $this->_getQuote()->removeAllAddresses();
            }

            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $quoteCheckoutMethod = $this->_getQuote()->getCheckoutMethod();
            if ($customer && $customer->getId()) {
                $this->_checkout->setCustomerWithAddressChange(
                    $customer, $this->_getQuote()->getBillingAddress(), $this->_getQuote()->getShippingAddress()
                );
            } elseif ((!$quoteCheckoutMethod
                    || $quoteCheckoutMethod != Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER)
                && !Mage::helper('checkout')->isAllowedGuestCheckout(
                    $this->_getQuote(),
                    $this->_getQuote()->getStoreId()
                )) {
                Mage::getSingleton('core/session')->addNotice(
                    Mage::helper('paypal')->__('To proceed to Checkout, please log in using your email address.')
                );
                $this->redirectLogin();
                Mage::getSingleton('customer/session')
                    ->setBeforeAuthUrl(Mage::getUrl('*/*/*', array('_current' => true)));
                return;
            }

            // billing agreement
            $isBARequested = (bool)$this->getRequest()
                ->getParam(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_BILLING_AGREEMENT);
            if ($isBARequested) {
                $this->_checkout->setIsBillingAgreementRequested($isBARequested);
            }

            // Bill Me Later
            if (method_exists($this->_checkout, 'setIsBml')) {  // This method only exists in later versions
                $this->_checkout->setIsBml((bool)$this->getRequest()->getParam('bml'));
            }

            // giropay
            $this->_checkout->prepareGiropayUrls(
                Mage::getUrl('checkout/onepage/success'),
                Mage::getUrl('paypal/express/cancel'),
                Mage::getUrl('checkout/onepage/success')
            );

            // Third parameter of the start method is only supported in later versions
            if ((version_compare(Mage::getVersion(), '1.14.0.0', '>=') &&
                    method_exists('Mage', 'getEdition') &&
                    Mage::getEdition() == Mage::EDITION_ENTERPRISE) ||
                (version_compare(Mage::getVersion(), '1.9.0.0', '>=') &&
                    method_exists('Mage', 'getEdition') &&
                    Mage::getEdition() == Mage::EDITION_COMMUNITY)) {

                $button = (bool)$this->getRequest()->getParam(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_BUTTON);
                $token = $this->_checkout->start(Mage::getUrl('*/*/return'), Mage::getUrl('*/*/cancel'), $button);
            } else {
                $token = $this->_checkout->start(Mage::getUrl('*/*/return'), Mage::getUrl('*/*/cancel'));
            }
            
            if ($token && $url = $this->_checkout->getRedirectUrl()) {
                $this->_initToken($token);
                $this->getResponse()->setRedirect($url);
                return;
            }
        } catch (Mage_Core_Exception $e) {
            $this->_getCheckoutSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getCheckoutSession()->addError($this->__('Unable to start Express Checkout.'));
            Mage::logException($e);
        }

        $this->_redirect('checkout/cart');
    }

    /**
     * Instantiate quote and checkout
     *
     * Note: The following three functions are private, so must be added here from the abstract.
     * No code changes were made to the methods.
     *
     * @throws Mage_Core_Exception
     */
    protected function _initCheckout()
    {
        // This method override only exists because the method was private
        // in previous versions. Later versions re-defined it as protected,
        // so in those versions we can just call parent
        if ((version_compare(Mage::getVersion(), '1.14.0.0', '>=') &&
                method_exists('Mage', 'getEdition') &&
                Mage::getEdition() == Mage::EDITION_ENTERPRISE) ||
            (version_compare(Mage::getVersion(), '1.9.0.0', '>=') &&
                method_exists('Mage', 'getEdition') &&
                Mage::getEdition() == Mage::EDITION_COMMUNITY)) {
            return parent::_initCheckout();
        }

        $quote = $this->_getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
            Mage::throwException(Mage::helper('paypal')->__('Unable to initialize Express Checkout.'));
        }
        $this->_checkout = Mage::getSingleton($this->_checkoutType, array(
            'config' => $this->_config,
            'quote'  => $quote,
        ));

        return $this->_checkout;
    }

    /**
     * Return checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        // This method override only exists because the method was private
        // in previous versions. Later versions re-defined it as protected,
        // so in those versions we can just call parent
        if ((version_compare(Mage::getVersion(), '1.14.0.0', '>=') &&
                method_exists('Mage', 'getEdition') &&
                Mage::getEdition() == Mage::EDITION_ENTERPRISE) ||
            (version_compare(Mage::getVersion(), '1.9.0.0', '>=') &&
                method_exists('Mage', 'getEdition') &&
                Mage::getEdition() == Mage::EDITION_COMMUNITY)) {
            return parent::_getCheckoutSession();
        }

        return Mage::getSingleton('checkout/session');
    }

    /**
     * Return checkout quote object
     *
     * @return Mage_Sale_Model_Quote
     */
    private function _getQuote()
    {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }
}
