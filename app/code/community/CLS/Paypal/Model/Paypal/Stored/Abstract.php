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
 * Contains common logic for the "Stored" methods family
 */
class CLS_Paypal_Model_Paypal_Stored_Abstract extends Mage_Payment_Model_Method_Abstract
{

    /**
     * Keeps the actual payment method instance that calls this common method
     *
     * @var $_callerMethod Mage_Payment_Model_Method_Abstract
     */
    protected $_callerMethod;

    /**
     * @param array $params
     */
    public function __construct($params = array())
    {
        parent::__construct($params);

        if ( !empty($params) && isset($params['caller_method']) ) {
            // Set '_callerMethod' property
            $this->_callerMethod = $params['caller_method'];
        }
        else {
            Mage::throwException(Mage::helper('cls_paypal')->__('Internal error: cannot initialize the payment method.'));
        }

    }

    /**
     * Retrieve payment information model object
     *
     * @return Mage_Payment_Model_Info
     */
    public function getInfoInstance()
    {
        $instance = $this->_callerMethod->getData('info_instance');

        if (!($instance instanceof Mage_Payment_Model_Info)) {
            Mage::throwException(Mage::helper('payment')->__('Cannot retrieve the payment information object instance.'));
        }
        return $instance;
    }

    /**
     * To check billing country is allowed for the payment method
     *
     * @return bool
     */
    public function canUseForCountry($country)
    {
        /*
         * for specific country, the flag will set up as 1
         */
        if($this->_callerMethod->getConfigData('allowspecific')==1){
            $availableCountries = explode(',', $this->_callerMethod->getConfigData('specificcountry'));
            if(!in_array($country, $availableCountries)){
                return false;
            }

        }
        return true;
    }

}
