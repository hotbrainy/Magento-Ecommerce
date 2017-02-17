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

class CLS_Paypal_Block_Paypal_Payment_Form_Orderstored extends Mage_Payment_Block_Form
{
    /**
     * Order instance of the original order
     * 
     * @var Mage_Sales_Model_Order
     */
    protected $_storedOrder = null;

    /**
     * Payment method instance from the original order
     *
     * @var $_storedPayment Mage_Sales_Model_Order_Payment|null
     */
    protected $_storedPayment = null;

    /**
     * Set custom template
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('cls_paypal/payment/form/order_stored.phtml');

        // Get original order payment
        /** @var $session Mage_Adminhtml_Model_Session_Quote */
        $session = Mage::getSingleton('adminhtml/session_quote');

        if (
            ($originalOrderId = $session->getPreviousOrderId())
            || ($originalOrderId = $session->getReordered())
            || ($originalOrderId = $session->getOrderId())
        ) {
            // Get original order data
            /** @var $originalOrder Mage_Sales_Model_Order */
            $originalOrder = Mage::getModel('sales/order')->load($originalOrderId);

            if ($originalOrder->getId()) {
                $this->_storedOrder = $originalOrder;
                
                $originalOrderPayment = $originalOrder->getPayment();

                if ($originalOrderPayment->getId()) {
                    $this->_storedPayment = $originalOrderPayment;
                }
            }
        }
    }
    
    /**
     * Get the original order's increment ID
     * 
     * @return string
     */
    public function getOrderIncrementId()
    {
       if (!is_null($this->_storedOrder)) {
           return $this->_storedOrder->getIncrementId();
       }
        
       return ''; 
    }

    /**
     * Get the original order's CC type
     *
     * @return string
     */
    public function getCcType()
    {
        if (!is_null($this->_storedPayment)) {
            return $this->_storedPayment->getCcType();
        }

        return '';
    }

    /**
     * Get the original order's CC last4
     *
     * @return string
     */
    public function getCcLast4()
    {
        if (!is_null($this->_storedPayment)) {
            return $this->_storedPayment->getCcLast4();
        }

        return '';
    }

}
