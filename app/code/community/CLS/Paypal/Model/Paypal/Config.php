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

class CLS_Paypal_Model_Paypal_Config extends Mage_Paypal_Model_Config
{
    const METHOD_PAYPAL_ORDERSTORED_AGREEMENT = 'paypal_orderstored_agreement';
    const METHOD_PAYFLOW_BILLING_AGREEMENT  = 'paypaluk_billing_agreement';
    const METHOD_PAYFLOW_ORDERSTORED_AGREEMENT = 'paypaluk_orderstored_agreement';

    // "Customer stored" payment methods
    const METHOD_PAYPAL_DIRECT_CUSTOMERSTORED          = 'paypal_direct_customerstored';
    const METHOD_PAYPAL_PAYFLOWADVANCED_CUSTOMERSTORED = 'payflow_advanced_customerstored';
    const METHOD_PAYPAL_PAYFLOWPRO_CUSTOMERSTORED      = 'verisign_customerstored';
    const METHOD_PAYPAL_PAYFLOWLINK_CUSTOMERSTORED     = 'payflow_link_customerstored';

    // "Order stored" payment methods
    const METHOD_PAYPAL_DIRECT_ORDERSTORED          = 'paypal_direct_orderstored';
    const METHOD_PAYPAL_PAYFLOWADVANCED_ORDERSTORED = 'payflow_advanced_orderstored';
    const METHOD_PAYPAL_PAYFLOWPRO_ORDERSTORED      = 'verisign_orderstored';
    const METHOD_PAYPAL_PAYFLOWLINK_ORDERSTORED     = 'payflow_link_orderstored';

    // Stored card lifetime (in months)
    const STORED_CARD_TTL_MONTHS = 12;

    /**
     * Fall-back payment method (to get missing config parameters)
     *
     * @var string
     */
    protected $_fallBackMethod = null;

    /**
     * Fallback method setter
     *
     * @param string $fallBackMethod
     * @return Mage_Paypal_Model_Config
     */
    public function setFallBackMethod($fallBackMethod)
    {
        $this->_fallBackMethod = $fallBackMethod;
        return $this;
    }

    /**
     * Whether to ask customer to create billing agreements
     * Unilateral payments are incompatible with the billing agreements
     *
     * @return bool
     */
    public function shouldAskToCreateBillingAgreement()
    {
        if ($this->getMethodCode() == Mage_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS) {
            return ($this->allow_ba_signup === Mage_Paypal_Model_Config::EC_BA_SIGNUP_ASK) && $this->business_account;
        }

        return parent::shouldAskToCreateBillingAgreement();
    }

    /**
     * Return list of allowed methods for specified country iso code
     *
     * @param string $countryCode 2-letters iso code
     * @return array
     */
    public function getCountryMethods($countryCode = null)
    {
        //Countries where this method is available
        // (based on countries where PayFlow Pro, PayFlow Express Checkout and Billing Agreements are available)
        $countriesByMethod = array(
            self::METHOD_PAYFLOW_BILLING_AGREEMENT => array(
                'US',
                'CA',
                'AU',
                'NZ',
            ),
            self::METHOD_PAYPAL_ORDERSTORED_AGREEMENT => array(
                'other',
                'US',
                'CA',
                'GB',
                'AU',
                'NZ',
                'JP',
                'FR',
                'IT',
                'ES',
                'HK',
            ),
            self::METHOD_PAYFLOW_ORDERSTORED_AGREEMENT => array(
                'other',
                'US',
                'CA',
                'GB',
                'AU',
                'NZ',
                'JP',
                'FR',
                'IT',
                'ES',
                'HK',
            ),
        );

        // PayPal Direct 'Stored card' methods
        $countriesByMethod[self::METHOD_PAYPAL_DIRECT_CUSTOMERSTORED] =
        $countriesByMethod[self::METHOD_PAYPAL_DIRECT_ORDERSTORED] = array(
            'US',
            'CA',
            'GB'
        );

        // PayPal Payflow-based 'Stored card' methods (Payflow Pro defines the list of supported countries)
        $countriesByMethod[self::METHOD_PAYPAL_PAYFLOWADVANCED_CUSTOMERSTORED] =
        $countriesByMethod[self::METHOD_PAYPAL_PAYFLOWLINK_CUSTOMERSTORED] =
        $countriesByMethod[self::METHOD_PAYPAL_PAYFLOWPRO_CUSTOMERSTORED] =
        $countriesByMethod[self::METHOD_PAYPAL_PAYFLOWADVANCED_ORDERSTORED] =
        $countriesByMethod[self::METHOD_PAYPAL_PAYFLOWLINK_ORDERSTORED] =
        $countriesByMethod[self::METHOD_PAYPAL_PAYFLOWPRO_ORDERSTORED] = array(
            'US',
            'CA',
            'AU',
            'NZ'
        );

        $countryMethods = parent::getCountryMethods($countryCode);

        foreach ($countriesByMethod as $methodCode => $countries) {
            //Add this method to the list of available methods in appropriate countries
            if (is_null($countryCode)) {
                foreach ($countries as $country) {
                    array_push($countryMethods[$country], $methodCode);
                }
            } elseif (in_array($countryCode, $countries)) {
                array_push($countryMethods, $methodCode);
            }
        }

        return $countryMethods;
    }

    /**
     * Map any supported payment method into a config path by specified field name
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _getSpecificConfigPath($fieldName)
    {
        $path = parent::_getSpecificConfigPath($fieldName);

        if (is_null($path)) {
            switch ($this->_methodCode) {
                case self::METHOD_PAYFLOW_BILLING_AGREEMENT:
                case self::METHOD_PAYFLOW_ORDERSTORED_AGREEMENT:
                    $path = $this->_mapMethodFieldset($fieldName);
                    if (is_null($path)) {
                        $path = $this->_mapWpukFieldset($fieldName);
                    }
                    break;
                case self::METHOD_PAYPAL_ORDERSTORED_AGREEMENT:
                    $path = $this->_mapMethodFieldset($fieldName);
                    if (is_null($path)) {
                        $path = $this->_mapWppFieldset($fieldName);
                    }
                    break;
                case self::METHOD_PAYPAL_DIRECT_CUSTOMERSTORED:
                case self::METHOD_PAYPAL_DIRECT_ORDERSTORED:
                    $path = $this->_mapStoredFieldset($fieldName);
                    break;
                default:
            }
        }

        return $path;
    }

    /**
     * Check whether method available for checkout or not
     * Logic based on merchant country, methods dependence
     *
     * @param string $methodCode
     * @return bool
     */
    public function isMethodAvailable($methodCode = null)
    {
        $result = parent::isMethodAvailable($methodCode);

        if (!$result) {
            return false;
        }

        // Additionally check parent method
        if ($methodCode === null) {
            $methodCode = $this->getMethodCode();
        }

        switch ($methodCode) {
            case self::METHOD_PAYPAL_DIRECT_CUSTOMERSTORED:
            case self::METHOD_PAYPAL_DIRECT_ORDERSTORED:
                if (!$this->isMethodActive(self::METHOD_WPP_DIRECT)) {
                    $result = false;
                }
                break;
            case self::METHOD_PAYPAL_PAYFLOWADVANCED_CUSTOMERSTORED:
            case self::METHOD_PAYPAL_PAYFLOWADVANCED_ORDERSTORED:
                if (!$this->isMethodActive(self::METHOD_PAYFLOWADVANCED)) {
                    $result = false;
                }
                break;
            case self::METHOD_PAYPAL_PAYFLOWLINK_CUSTOMERSTORED:
            case self::METHOD_PAYPAL_PAYFLOWLINK_ORDERSTORED:
                if (!$this->isMethodActive(self::METHOD_PAYFLOWLINK)) {
                    $result = false;
                }
                break;
            case self::METHOD_PAYPAL_PAYFLOWPRO_CUSTOMERSTORED:
            case self::METHOD_PAYPAL_PAYFLOWPRO_ORDERSTORED:
                if (!$this->isMethodActive(self::METHOD_PAYFLOWPRO)) {
                    $result = false;
                }
                break;
        }

        return $result;
    }

    /**
     * Map PayPal Website Payments Pro common config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapWpukFieldset($fieldName)
    {
        $result = null;
        switch ($this->_methodCode) {
            case self::METHOD_PAYFLOW_BILLING_AGREEMENT:
            case self::METHOD_PAYFLOW_ORDERSTORED_AGREEMENT:
                $pathPrefix = 'paypal/wpuk';
                // Use PUMP credentials from Verisign for EC when Direct Payments are unavailable
                if (!$this->isMethodAvailable(Mage_Paypal_Model_Config::METHOD_WPP_PE_DIRECT)) {
                    $pathPrefix = 'payment/verisign';
                }
                switch ($fieldName) {
                    case 'partner':
                    case 'user':
                    case 'vendor':
                    case 'pwd':
                    case 'sandbox_flag':
                    case 'use_proxy':
                    case 'proxy_host':
                    case 'proxy_port':
                        $result = $pathPrefix . '/' . $fieldName;
                        break;
                    default:
                }
                break;

            default:
                $result = parent::_mapWpukFieldset($fieldName);
        }

        return $result;
    }

    /**
     * Map 'Stored card' common config fields
     *
     * @param string $fieldName
     * @return string|null
     */
    protected function _mapStoredFieldset($fieldName)
    {
        switch ($fieldName)
        {
            // The following fields should be taken from the actual 'Stored' payment method
            case 'active':
            case 'title':
            case 'sort_order':
            case 'payment_action':
            case 'allowspecific':
            case 'specificcountry':
            case 'debug':
            case 'verify_peer':
            case 'line_items_enabled':
                return "payment/{$this->_methodCode}/{$fieldName}";
            default:
                if (!is_null($this->_fallBackMethod)) {
                    // Fall back to the parent payment method
                    switch ($this->_fallBackMethod)
                    {
                        case self::METHOD_WPP_DIRECT:
                            return $this->_mapWppFieldset($fieldName);
                        default:
                            return null;
                    }
                }

                return null;
        }
    }
}
