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

class CLS_Paypal_Helper_Paypal_Data extends Mage_Paypal_Helper_Data
{
    protected $_baShouldAskByMethod = array();

    /**
     * Check whether customer should be asked confirmation whether to sign a billing agreement
     *
     * @param Mage_Paypal_Model_Config $config
     * @param int $customerId
     * @return bool
     */
    public function shouldAskToCreateBillingAgreement(Mage_Paypal_Model_Config $config, $customerId)
    {
        if (!isset($this->_baShouldAskByMethod[$config->getMethodCode()])) {
            $this->_baShouldAskByMethod[$config->getMethodCode()] = false;
            if ($customerId && $config->shouldAskToCreateBillingAgreement()) {
                if (Mage::getModel('sales/billing_agreement')->needToCreateForCustomer($customerId)) {
                    $this->_baShouldAskByMethod[$config->getMethodCode()] = true;
                }
            } elseif (!$customerId && $config->shouldAskToCreateBillingAgreement()) {
                $this->_baShouldAskByMethod[$config->getMethodCode()] = true;
            }
        }
        return $this->_baShouldAskByMethod[$config->getMethodCode()];
    }
}
