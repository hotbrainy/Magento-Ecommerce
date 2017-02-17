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

class CLS_Paypal_Block_Customer_Storedcard extends Mage_Core_Block_Template
{

    /**
     * Return the list of customer's stored cards
     *
     * @return CLS_Paypal_Model_Resource_Customerstored_Collection|null
     */
    public function getStoredCards()
    {
        if (is_null($this->getData('stored_cards'))) {
            $customerId = $this->getCustomer()->getId();

            if (!$customerId) {
                return null;
            }

            $collection = Mage::getSingleton('cls_paypal/customerstored')->getCustomerstoredCollection($customerId);
            $this->setData('stored_cards', $collection);
        }

        return $this->getData('stored_cards');
    }

    /**
     * Transform date to store format
     *
     * @param string $date
     * @return Zend_Date
     */
    public function transformDate($date)
    {
        return Mage::app()->getLocale()->storeDate(
            $this->getCustomer()->getStoreId(),
            strtotime($date)
        );
    }

    /**
     * Return current customer model
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        $customer = $this->getData('customer');
        if (is_null($customer)) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->setData('customer', $customer);
        }
        return $customer;
    }

    /**
     * Build 'Delete' URL
     *
     * @param CLS_Paypal_Model_Customerstored $storedCard
     * @return string
     */
    public function getDeleteUrl($storedCard)
    {
        return $this->getUrl('*/*/delete', array('stored_card_id' => $storedCard->getId()));
    }

}
