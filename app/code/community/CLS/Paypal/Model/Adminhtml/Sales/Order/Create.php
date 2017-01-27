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

class CLS_Paypal_Model_Adminhtml_Sales_Order_Create extends Mage_Adminhtml_Model_Sales_Order_Create
{
    /**
     * Create new order
     *
     * @return Mage_Sales_Model_Order
     * @see Mage_Adminhtml_Model_Sales_Order_Create
     */
    public function createOrder()
    {
        if ((version_compare('1.12.0.2', Mage::getVersion(), '>=') &&
                method_exists('Mage', 'getEdition') &&
                Mage::getEdition() == Mage::EDITION_ENTERPRISE) ||
            (version_compare('1.7.0.2', Mage::getVersion(), '>=') &&
                method_exists('Mage', 'getEdition') &&
                Mage::getEdition() == Mage::EDITION_COMMUNITY)) {
            return parent::createOrder();
        }

        $this->_prepareCustomer();
        $this->_validate();
        $quote = $this->getQuote();
        $this->_prepareQuoteItems();

        $service = Mage::getModel('sales/service_quote', $quote);
        /** @var Mage_Sales_Model_Order $oldOrder */
        $oldOrder = $this->getSession()->getOrder();
        if ($oldOrder->getId()) {
            $originalId = $oldOrder->getOriginalIncrementId();
            if (!$originalId) {
                $originalId = $oldOrder->getIncrementId();
            }
            $orderData = array(
                'original_increment_id'     => $originalId,
                'relation_parent_id'        => $oldOrder->getId(),
                'relation_parent_real_id'   => $oldOrder->getIncrementId(),
                'edit_increment'            => $oldOrder->getEditIncrement()+1,
                'increment_id'              => $originalId.'-'.($oldOrder->getEditIncrement()+1)
            );
            $quote->setReservedOrderId($orderData['increment_id']);
            $service->setOrderData($orderData);

            $oldOrder->cancel();
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = $service->submit();
        $customer = $quote->getCustomer();
        if ((!$customer->getId() || !$customer->isInStore($this->getSession()->getStore()))
            && !$quote->getCustomerIsGuest()
        ) {
            $customer->setCreatedAt($order->getCreatedAt());
            $customer
                ->save()
                ->sendNewAccountEmail('registered', '', $quote->getStoreId());;
        }

        if ($oldOrder->getId()) {
            $oldOrder->setRelationChildId($order->getId());
            $oldOrder->setRelationChildRealId($order->getIncrementId());
            $oldOrder->save();
            $order->save();
        }
        if ($this->getSendConfirmation()) {
            $order->sendNewOrderEmail();
        }

        Mage::dispatchEvent('checkout_submit_all_after', array('order' => $order, 'quote' => $quote));

        return $order;
    }
}