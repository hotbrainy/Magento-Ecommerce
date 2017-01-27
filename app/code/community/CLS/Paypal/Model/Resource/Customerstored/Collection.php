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

class CLS_Paypal_Model_Resource_Customerstored_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('cls_paypal/customerstored');
    }

    /**
     * Apply filter by customer
     *
     * @param int $customerId
     * @return CLS_Paypal_Model_Resource_Customerstored_Collection
     */
    public function filterByCustomerId($customerId)
    {
        $this->getSelect()->where('customer_id = ?', $customerId);

        return $this;
    }

    /**
     * Apply filter by card active state (based on last usage date)
     *
     * @return CLS_Paypal_Model_Resource_Customerstored_Collection
     */
    public function filterByActiveState()
    {
        $minDate = new Zend_Date(null);
        $minDate->addMonth(0 - CLS_Paypal_Model_Paypal_Config::STORED_CARD_TTL_MONTHS);         
        $this->getSelect()->where('date >= ?', date('Y-m-d', $minDate->get(Zend_Date::TIMESTAMP)));

        return $this;
    }

    /**
     * Apply filter by card expiration date (extract valid cards only)
     *
     * @return CLS_Paypal_Model_Resource_Customerstored_Collection
     */
    public function filterByExpirationDate()
    {
        $now = new Zend_Date(null);
        $dateArray = $now->toArray();

        $this->getSelect()->where("
            (cc_exp_year > '{$dateArray['year']}') OR
            (cc_exp_year = '{$dateArray['year']}' AND cc_exp_month >= {$dateArray['month']})
        ");

        return $this;
    }

    /**
     * Apply filter by payment method
     *
     * @param array $paymentMethod
     * @return CLS_Paypal_Model_Resource_Customerstored_Collection
     */
    public function filterByPaymentMethod(array $paymentMethod)
    {
        if (!empty($paymentMethod)) {
            $this->getSelect()->where('payment_method IN (?)', $paymentMethod);
        }

        return $this;
    }
}
