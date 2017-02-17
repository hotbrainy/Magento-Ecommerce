<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
 
class MageWorx_CustomerCredit_Adminhtml_Mageworx_Customercredit_CreditController extends Mage_Adminhtml_Controller_Action
{
    protected function _initCustomer($idFieldName = 'id') {
        $customerId = (int) $this->getRequest()->getParam($idFieldName);
        $customerModel = Mage::getModel('customer/customer');
        if ($customerId) {
            $customerModel->load($customerId);
        }
        if (!$customerModel->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mageworx_customercredit')->__('The customer not found'));
        }
        Mage::register('current_customer', $customerModel);
        return $this;
    }
    
    public function indexAction() {
        $this->_initCustomer();
        $this->loadLayout()
            ->renderLayout();
    }
    
    public function logGridAction() {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mageworx_customercredit/adminhtml_customer_edit_tab_customercredit_log_grid')->toHtml()
        );
    }
    
    public function createProductAction() {        
        if (!Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->getIdBySku('customercredit')) {
            $productId = Mage::helper('mageworx_customercredit')->createCreditProduct();

            if ($productId) {
                Mage::getModel('core/config_data')->load('mageworx_customercredit/main/credit_product', 'path')
                    ->setValue('customercredit')
                    ->setPath('mageworx_customercredit/main/credit_product')
                    ->save();                
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mageworx_customercredit')->__('Credit product was successfully created.'));
            } else {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mageworx_customercredit')->__('Failed to create credit product.'));
            }
        }    
        $this->_redirectReferer();
    }
    
    public function syncAction() {
        $syncType = $this->getRequest()->getParam('sync_type',MageWorx_CustomerCredit_Model_System_Config_Source_Sync::ACTION_TYPE_APPEND);
        $rewardPointCollection = Mage::getResourceModel('enterprise_reward/reward_collection');
        if(!$rewardPointCollection) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mageworx_customercredit')->__('Failed to sync credit balances. Please check Magento version.'));
            return $this->_redirectReferer();
        }
        foreach ($rewardPointCollection as $item) {
            $customerCredit = Mage::getModel('mageworx_customercredit/credit', $item->getCustomer());
            $customerCredit->processSync($syncType, $item);
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mageworx_customercredit')->__('All data were synchronized.'));
        return $this->_redirectReferer();
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('customer/manage');
    }
}