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

class CLS_Paypal_Model_Paypal_Info extends Mage_Paypal_Model_Info
{

    const BILLING_AGREEMENT_ID = 'billing_agreement_id';

    /**
     * Initialize the Info model
     *
     */
    public function __construct()
    {
        $this->_paymentMap[self::BILLING_AGREEMENT_ID] = Mage_Sales_Model_Payment_Method_Billing_AgreementAbstract::PAYMENT_INFO_REFERENCE_ID;
    }

    /**
     * Render info item labels
     *
     * @param string $key
     */
    protected function _getLabel($key)
    {
        switch ($key) {
            case Mage_Sales_Model_Payment_Method_Billing_AgreementAbstract::PAYMENT_INFO_REFERENCE_ID:
                return Mage::helper('cls_paypal')->__('Billing Agreement Id');
        }
        return parent::_getLabel($key);
    }
}
