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

class CLS_Paypal_Model_Resource_Customerstored extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Initialize main table and table id field
     */
    protected function _construct()
    {
        $this->_init('cls_paypal/customerstored', 'stored_card_id');
    }

    /**
     * Check card for duplicates
     *
     * @param int $customerId
     * @param string $ccType
     * @param string $ccLast4
     * @param string $ccExpMonth
     * @param string $ccExpYear
     * @param array $compatiblePaymentMethods
     * @return array
     */
    public function checkCardDuplicate($customerId, $ccType, $ccLast4, $ccExpMonth, $ccExpYear, $compatiblePaymentMethods)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select();

        $binds = array(
            'customer_id'   => $customerId,
            'cc_type'       => $ccType,
            'cc_last4'      => $ccLast4,
            'cc_exp_month'  => $ccExpMonth,
            'cc_exp_year'   => $ccExpYear
        );

        $select
            ->from($this->getMainTable(), array('stored_card_id'))
            ->where('customer_id = :customer_id')
            ->where('cc_type = :cc_type')
            ->where('cc_last4 = :cc_last4')
            ->where('cc_exp_month = :cc_exp_month')
            ->where('cc_exp_year = :cc_exp_year');
        
        if (!empty($compatiblePaymentMethods)) {
            $select->where('payment_method IN (?)', $compatiblePaymentMethods);
        }

        return $read->fetchCol($select, $binds);
    }
}
