<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_RefferalsController extends Mage_Core_Controller_Front_Action {

    private $_customerCredit = 0;
    
    public function indexAction() {
        $this->loadLayout();
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('mageworx_customercredit')->__('My Sharring List'));
        $this->renderLayout();
    }
    
    /**
     * Generate codes
     * @return url
     */
    public function generateAction() {
        $helper = Mage::helper('mageworx_customercredit');
        $credit = (float)$this->getRequest()->getParam('credit_value');
        $qty    = 1;
        
        if(!$credit) {
             Mage::getSingleton('core/session')->addError($this->__("Credit value is required value."));
            return $this->_redirectReferer();
        }
        
        if($credit<MageWorx_CustomerCredit_Block_Customer_Refferals_Details::CC_MIN_CREDIT_CODE) {
            Mage::getSingleton('core/session')->addError($this->__("Please increase code value."));
            return $this->_redirectReferer();
        }
        if($credit>MageWorx_CustomerCredit_Block_Customer_Refferals_Details::CC_MAX_CREDIT_CODE) {
            Mage::getSingleton('core/session')->addError($this->__("Please decrease code value."));
            return $this->_redirectReferer();
        }
        
        if($qty>MageWorx_CustomerCredit_Block_Customer_Refferals_Details::CC_MAX_QTY) {
            Mage::getSingleton('core/session')->addNotice($this->__("Max code number is %s. Value was changed to maximum.",MageWorx_CustomerCredit_Block_Customer_Refferals_Details::CC_MAX_QTY));
            $qty = MageWorx_CustomerCredit_Block_Customer_Refferals_Details::CC_MAX_QTY;
        }
        
        $customer = Mage::getModel('customer/session')->getCustomer();
        $qty = $this->checkCustomerBalance($customer,$credit,$qty);
        
        if($qty<1) {
            Mage::getSingleton('core/session')->addError($this->__("You can't create code with %s value. Your balance is %s", Mage::helper('core')->currency($credit), Mage::helper('core')->currency($this->_customerCredit)));
            return $this->_redirectReferer();
        }
        $data = array('qty'=>$qty,
            'code_length'       => $helper->getCodeLength(),
            'group_length'      => $helper->getGroupLength(),
            'group_separator'   => $helper->getGroupSeparator(),
            'code_format'       => $helper->getCodeFormat()
        );
        $codeModel = Mage::getModel('mageworx_customercredit/code');
        $codeModel->setIsNew(true)
                ->setCredit($credit)
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->setIsActive(true)
                ->setOwnerId($customer->getId())
                ->setGenerate($data);
        try {
            $codeModel->generate();
            $lastItem = $codeModel->getCollection()->getLastItem();
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addException($e, $this->__('There was a problem with generate codes: %s', $e->getMessage()));
            return $this->_redirectReferer();
        }

        $creditModel = Mage::getModel('mageworx_customercredit/credit', $customer);
        $creditModel->processDecreaseCredit($qty*$credit);

        Mage::getSingleton('core/session')->addSuccess($this->__('Credit Code %s was created.',$lastItem->getCode()));
        return $this->_redirectReferer();
    }
    
    /**
     * Check balance
     * @param Mage_Customer_Model_Customer $customer
     * @param flaot $credit
     * @param int $qty
     * @return int
     */
    public function checkCustomerBalance($customer, $credit, $qty)
    {
        $customerCredit = Mage::getModel('mageworx_customercredit/credit', $customer)->getValue();
        $this->_customerCredit = $customerCredit;
        $codeCredit = $credit*$qty;
        if($customerCredit>=$codeCredit) {
            return $qty;
        } else {
            $qty = $customerCredit/$credit;
            $qty = (int)$qty;
            if($qty>0) {
                Mage::getSingleton('core/session')->addNotice($this->__('Number of codes was changed to %s.',$qty));
            }
            return $qty;
        }
    }
}