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

class CLS_Paypal_Model_Paypaluk_Api_Nvp_Common extends Varien_Object
{
    protected $_nvpInstance = null;

    /**
     * Paypal methods definition
     */
    const SET_BILLING_AGREEMENT = 'SetCustomerBillingAgreement';
    const CREATE_BILLING_AGREEMENT = 'CreateBillingAgreement';
    const UPDATE_BILLING_AGREEMENT = 'BillAgreementUpdate';
    const DO_REFERENCE_TRANSACTION = 'DoReferenceTransaction';

    /**
     * Actions
     */
    const BA_SET = 'S';
    const BA_CREATE = 'X';
    const BA_UPDATE = 'U';

    const RESPONSE_MSG = 'paypal_response_msg';

    /**
     * Create Billing Agreement request/response map
     * @var array
     */
    public $createBillingAgreementResponse = array('BAID');

    /**
     * Update Billing Agreement request/response map
     * @var array
     */
    public $updateBillingAgreementRequest = array(
        'BAID', 'TENDER', 'BA_STATUS',
    );
    public $updateBillingAgreementResponse = array(
        'BAID', 'PPREF', 'RESPMSG', 'CORRELATIONID',
    );

    /**
     * Do Reference Transaction request/response map
     *
     * @var array
     */
    public $doReferenceTransactionRequest = array('BAID', 'TENDER', 'AMT', 'FREIGHTAMT',
        'TAXAMT', 'INVNUM', 'NOTIFYURL'
    );
    public $doReferenceTransactionResponse = array('BAID', 'PNREF', 'PPREF', 'PENDINGREASON', 'RESPMSG');

    public function __construct($params=array())
    {
        // Initialize common method
        if ( empty($params) || !isset($params['nvp_instance']) ) {
            Mage::throwException(Mage::helper('cls_paypal')->__('Internal error: cannot initialize common PayFlow Pro API model.'));
        }

        $this->_nvpInstance = $params['nvp_instance'];
    }

    /**
     * Add necessary components to the global map
     *
     * @param array $map
     * @return array
     */
    public function processGlobalMap($map)
    {
        $map['BILLINGTYPE'] = 'billing_type';
        $map['BAID'] = 'billing_agreement_id';
        $map['RESPMSG'] = 'response_msg';
        $map['BA_STATUS'] = 'billing_agreement_status';
        $map['BUTTONSOURCE'] = 'paypal_info_code';

        return $map;
    }

    /**
     * Add/change components on individual request/response key maps
     *
     * @param string $key
     * @param array $map
     * @return array
     */
    public function processTransactionMap($key, $map)
    {
        switch ($key) {
            case 'create_ba_request':
                $map[] = 'TENDER';
                break;
            case 'create_ba_response':
                $map = $this->createBillingAgreementResponse;
                break;
            case 'update_ba_request':
                $map = $this->updateBillingAgreementRequest;
                break;
            case 'update_ba_response':
                $map = $this->updateBillingAgreementResponse;
                break;
            case 'do_ref_trans_request':
                $map = $this->doReferenceTransactionRequest;
                break;
            case 'do_ref_trans_response':
                $map = $this->doReferenceTransactionResponse;
                break;
            case 'debug_replace_private':
                $map[] = 'BUTTONSOURCE';
                break;
            case 'set_expr_checkout_request':
                $map[] = 'BILLINGTYPE';
                break;
            case 'customer_ba_request':
                $map[] = 'TENDER';
                $map[] = 'AMT';
                break;
            default:
        }

        return $map;
    }

    /**
     * Get Paypal info code
     *
     * @return string
     */
    public function getPaypalInfoCode()
    {
        return Mage::helper('cls_paypal')->getPaypalInfoCode();
    }

    /**
     * Return PaypalUk tender based on config data
     *
     * @param Mage_Paypal_Model_Config $config
     * @return string | bool
     */
    public function getTender($config)
    {
        switch ($config->getMethodCode()) {
            case CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOW_BILLING_AGREEMENT:
            case CLS_Paypal_Model_Paypal_Config::METHOD_PAYFLOW_ORDERSTORED_AGREEMENT:
                return Mage_PaypalUk_Model_Api_Nvp::TENDER_PAYPAL;

            default:
                return false;
        }
    }

    /**
     * Return Payflow Edition
     *
     * @param string
     * @return string | bool
     */
    public function getPaypalUkActionName($methodName)
    {
        switch($methodName) {
            case self::DO_REFERENCE_TRANSACTION:
                return Mage_PaypalUk_Model_Api_Nvp::EXPRESS_DO_PAYMENT;

            case self::SET_BILLING_AGREEMENT:
                return self::BA_SET;

            case self::CREATE_BILLING_AGREEMENT:
                return self::BA_CREATE;

            case self::UPDATE_BILLING_AGREEMENT:
                return self::BA_UPDATE;

            default:
                return false;
        }
    }

    /**
     * Map paypal method names
     *
     * @param string| $methodName
     * @param Mage_Paypal_Model_Config $config
     * @return string
     */
    public function mapPaypalMethodName($methodName, $config)
    {
        switch ($methodName) {
            case self::DO_REFERENCE_TRANSACTION:
                return ($config->payment_action == Mage_Paypal_Model_Config::PAYMENT_ACTION_AUTH)
                    ? Mage_PaypalUk_Model_Api_Nvp::TRXTYPE_AUTH_ONLY
                    : Mage_PaypalUk_Model_Api_Nvp::TRXTYPE_SALE;

            case self::SET_BILLING_AGREEMENT:
            case self::CREATE_BILLING_AGREEMENT:
                return Mage_PaypalUk_Model_Api_Nvp::TRXTYPE_AUTH_ONLY;

            case self::UPDATE_BILLING_AGREEMENT:
                return null;

            default:
                return false;
        }
    }

    /***
     * Processing before the "set billing agreement" request is sent
     */
    public function preCallSetCustomerBillingAgreement()
    {
        $this->_nvpInstance->setAmount(0);
    }
}