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

class CLS_Paypal_Customer_StoredcardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Retrieve customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }

    public function preDispatch()
    {
        parent::preDispatch();

        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * List customer's stored cards
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');

        $this->getLayout()->getBlock('head')->setTitle($this->__('My Credit Cards'));

        $this->renderLayout();
    }

    /**
     * Delete stored card
     */
    public function deleteAction()
    {
        $storedCardId = (int)$this->getRequest()->getParam('stored_card_id');

        if ($storedCardId) {
            $storedCardModel = Mage::getModel('cls_paypal/customerstored')->load($storedCardId);

            $customerId = $this->_getSession()->getCustomerId();

            // Perform a security check
            if (
                $storedCardModel->getId()
                && ($storedCardModel->getCustomerId() == $customerId)
            ) {
                // Delete card
                $storedCardModel->delete();

                $this->_getSession()->addSuccess($this->__('The card had been deleted.'));
            }
        }

        $this->_redirect('*/*/');
    }
}
