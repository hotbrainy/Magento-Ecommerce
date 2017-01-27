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

class CLS_Paypal_Model_Paypaluk_Api_Nvp extends Mage_PaypalUk_Model_Api_Nvp
{
    protected $_commonNvp = null;

    protected function _construct()
    {
        parent::_construct();

        $this->_commonNvp = Mage::getModel('cls_paypal/paypaluk_api_nvp_common', array('nvp_instance' => $this));

        $this->_globalMap = $this->_commonNvp->processGlobalMap($this->_globalMap);

        $this->_createBillingAgreementRequest = $this->_commonNvp->processTransactionMap('create_ba_request', $this->_createBillingAgreementRequest);
        $this->_createBillingAgreementResponse = $this->_commonNvp->processTransactionMap('create_ba_response', $this->_createBillingAgreementResponse);
        $this->_updateBillingAgreementRequest = $this->_commonNvp->processTransactionMap('update_ba_request', $this->_updateBillingAgreementRequest);
        $this->_updateBillingAgreementResponse = $this->_commonNvp->processTransactionMap('update_ba_response', $this->_updateBillingAgreementResponse);
        $this->_doReferenceTransactionRequest = $this->_commonNvp->processTransactionMap('do_ref_trans_request', $this->_doReferenceTransactionRequest);
        $this->_doReferenceTransactionResponse = $this->_commonNvp->processTransactionMap('do_ref_trans_response', $this->_doReferenceTransactionResponse);
        $this->_debugReplacePrivateDataKeys = $this->_commonNvp->processTransactionMap('debug_replace_private', $this->_debugReplacePrivateDataKeys);
        $this->_setExpressCheckoutRequest = $this->_commonNvp->processTransactionMap('set_expr_checkout_request', $this->_setExpressCheckoutRequest);
        $this->_customerBillingAgreementRequest = $this->_commonNvp->processTransactionMap('customer_ba_request', $this->_customerBillingAgreementRequest);
    }

    /**
     * Get Paypal info code
     *
     * @return string
     */
    public function getPaypalInfoCode()
    {
        return $this->_commonNvp->getPaypalInfoCode();
    }
    
    /**
     * Return PaypalUk tender based on config data
     *
     * @return string
     */
    public function getTender()
    {
        $value = $this->_commonNvp->getTender($this->_config);
        if ($value === FALSE) {
            $value = parent::getTender();
        }
        return $value;
    }

    /**
     * Add method to request array
     *
     * @param string $methodName
     * @param array $request
     * @return array
     */
    protected function _addMethodToRequest($methodName, $request)
    {
        $txnType = $this->_mapPaypalMethodName($methodName);
        if ($txnType) {
            return parent::_addMethodToRequest($methodName, $request);
        } else {
            if (!is_null($this->_getPaypalUkActionName($methodName))) {
                $request['ACTION'] = $this->_getPaypalUkActionName($methodName);
            }
            return $request;
        }
    }

    /**
     * Return Payflow Edition
     *
     * @param string
     * @return string | null
     */
    protected function _getPaypalUkActionName($methodName)
    {
        $value = $this->_commonNvp->getPaypalUkActionName($methodName);
        if ($value === FALSE) {
            $value = parent::_getPaypalUkActionName($methodName);
        }
        return $value;
    }

    /**
     * Map paypal method names
     *
     * @param string| $methodName
     * @return string
     */
    protected function _mapPaypalMethodName($methodName)
    {
        $value = $this->_commonNvp->mapPaypalMethodName($methodName, $this->_config);
        if ($value === FALSE) {
            $value = parent::_mapPaypalMethodName($methodName);
        }
        return $value;
    }

    /**
     * Set Customer Billing Agreement call
     *
     * @link https://cms.paypal.com/us/cgi-bin/?&cmd=_render-content&content_ID=developer/e_howto_api_nvp_r_SetCustomerBillingAgreement
     */
    public function callSetCustomerBillingAgreement()
    {
        $this->_commonNvp->preCallSetCustomerBillingAgreement();
        parent::callSetCustomerBillingAgreement();
    }
}
