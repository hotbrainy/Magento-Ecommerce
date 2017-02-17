<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_IndexController extends Mage_Core_Controller_Front_Action {

    /**
     * Action predispatch
     * Check customer authentication for some actions
     * @return MageWorx_CustomerCredit_IndexController
     */
    public function preDispatch() {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return;
        }
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            return;
        }
        if (!Mage::helper('mageworx_customercredit')->isShowCustomerCredit())
        {
            $this->norouteAction();
            return;
        }

        if (!Mage::helper('mageworx_customercredit')->isEnabled()) {
            $this->norouteAction();
            return;
        }
        return $this;
    }

    public function indexAction() {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->loadLayoutUpdates();

        $data = Mage::getSingleton('customer/session')->getCustomercreditFormData(true);
        Mage::register('customercredit_code', new Varien_Object());
        if (!empty($data)) {
            Mage::registry('customercredit_code')->addData($data);
        }

        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('mageworx_customercredit')->__('My Credit'));
        $this->renderLayout();
    }

    public function logAction() {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->loadLayoutUpdates();
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('mageworx_customercredit')->__('My Credit Activity'));
        if ($block = $this->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_getRefererUrl());
        }
        $this->renderLayout();
    }

    public function refillAction() {
        if (!Mage::helper('mageworx_customercredit')->isEnabledCodes())
            $this->_forward('index');
        if ($this->getRequest()->has('customercredit_code')) {
            $code = $this->getRequest()->getPost('customercredit_code');
            try {
                $codeModel = Mage::getModel('mageworx_customercredit/code')->loadByCode($code);
                $refillCredit = $codeModel->getCredit();
                $codeModel->useCode(Mage::getSingleton('customer/session')->getCustomer());

                Mage::getSingleton('customer/session')->addSuccess($this->__(
                                'Credit Balance was refilled with %s successfully using Recharge Code "%s".', Mage::helper('core')->currency($refillCredit), Mage::helper('core')->htmlEscape($codeModel->getCode()))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('customer/session')->setCustomercreditFormData($this->getRequest()->getPost())
                        ->addError($e->getMessage());
            } catch (Exception $e) {
                Mage::getSingleton('customer/session')->addException($e, $this->__('Error occur while refilling the credit.'));
            }
            $this->_redirect('*');
        }
    }
    
    public function removeCodeAction() {
        $codeId = $this->getRequest()->getParam('code_id');
        $codeModel = Mage::getModel('mageworx_customercredit/code')->load($codeId);

        $customerId = $codeModel->getOwnerId();
        $customer = Mage::getModel('customer/customer')->load($customerId);

        $creditModel = Mage::getModel('mageworx_customercredit/credit', $customer);
        $creditModel->processRefill($codeModel);
        
        try {
            $codeModel->delete();
            Mage::getSingleton('customer/session')->addSuccess($this->__('The code %s was successfully removed.',$codeModel->getCode()));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('customer/session')->setCustomercreditFormData($this->getRequest()->getPost())
                    ->addError($e->getMessage());
        }
        return $this->_redirectReferer();
        
    }

    public function updateCreditPostAction() {
        Mage::getSingleton('checkout/session')->setUseInternalCredit(true);
        $this->_redirect('checkout/cart');
    }

    public function removeCreditUseAction() {
        Mage::getSingleton('checkout/session')->setUseInternalCredit(false);
        Mage::getSingleton('customer/session')->unsCustomCreditValue();
        $this->_redirect('checkout/cart');
    }

    /**
     * Change patyment credit
     */
    private function _changeCredit() {
        $helper = Mage::helper('mageworx_customercredit');
        $creditValue     = $this->getRequest()->getParam('custmer_credit_value');
        $credit          = $helper->getValueExchangeRateDivided($creditValue);
        $realCreditValue = $helper->getRealCreditValue();

        $shippingAddress = Mage::getModel('checkout/cart')->getQuote()->getShippingAddress();
        $shipping        = floatval($shippingAddress->getShippingAmount() - $shippingAddress->getShippingTaxAmount());
        $subtotal        = floatval(Mage::getModel('checkout/cart')->getQuote()->getSubtotalWithDiscount());
        $tax             = floatval($shippingAddress->getTaxAmount());
        $total           = $subtotal + $shipping + $tax;
        
        $creditTotals = $helper->getCreditTotals();
        if (count($creditTotals)<3) {
            $creditLeft = 0;
            foreach ($creditTotals as $field) {
                switch ($field) {
                    case 'subtotal':                            
                        $creditLeft += $subtotal;
                        break;
                    case 'shipping':
                        $creditLeft += $shipping;                   
                        break;
                    case 'tax':
                        $creditLeft += $tax;
                        break;    
                    case 'fees':
                        $baseCreditLeft += $shippingAddress->getBaseMultifeesAmount();
                        $creditLeft += $shippingAddress->getMultifeesAmount();
                        break;  
                }
            }
        } else {
            $creditLeft = $total;
        }
        if ($credit > $creditLeft) {
            $credit = $creditLeft;
        }

        $creditValue = $helper->getValueExchangeRateMultiplied($credit);
        if($creditValue > $realCreditValue) {
            $creditValue = $realCreditValue;
        }

        Mage::getSingleton('customer/session')->setCustomCreditValue($creditValue);
        return true;
    }
    
    public function reload_paymentAction()
    {
        $this->_changeCredit();
        $helper = Mage::helper('mageworx_customercredit');
        $session = Mage::getSingleton('checkout/session');
        $session->setUseInternalCredit(true);
        Mage::getModel('checkout/cart')->getQuote()->collectTotals();
        $this->loadLayout();

        $selectedPartialMethod = $this->getRequest()->getParam('selected_partial_method');
        $creditValue     = $this->getRequest()->getParam('custmer_credit_value');
        $realCreditValue = $helper->getRealCreditValue();

        if ($selectedPartialMethod && $creditValue < $realCreditValue) {
            $block = $this->getLayout()->getBlock('root');
            $block->setSelectedPartialMethod($selectedPartialMethod);
        }

        $this->renderLayout();
    }
    
    public function reload_payment_ajaxAction()
    {
        $this->_changeCredit();
        $session = Mage::getSingleton('checkout/session');
        $session->setUseInternalCredit(true);
        Mage::getModel('checkout/cart')->getQuote()->collectTotals();
    }
    
    public function reloadCartAction() {
        $helper = Mage::helper('mageworx_customercredit');
        $creditValue     = $this->getRequest()->getParam('custmer_credit_value');
        $realCreditValue = $helper->getRealCreditValue();

        if ($creditValue > $realCreditValue) {
            $session = Mage::getSingleton('checkout/session');
            $session->getMessages(true);
            $session->addError($helper->__('Entered credit amount is not available'));
        } else {
            $this->_changeCredit();
        }
        return $this->_redirect('checkout/cart/');
    }

    public function testCronAction() {
        Mage::getModel('mageworx_customercredit/observer')->expirationDateCron();
        Mage::getModel('mageworx_customercredit/observer')->checkCustomerBirthdayCron();
        Mage::getModel('mageworx_customercredit/observer')->expirationDateRefreshCron();
    }
}