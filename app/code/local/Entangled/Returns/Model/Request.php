<?php

class Entangled_Returns_Model_Request extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('entangled_returns/request');
    }

    public function _beforeSave(){
        parent::_beforeSave();
        return $this->checkReturnAvailability();
    }

    public function getReasonLabel(){
        $reasons = Mage::helper('entangled_returns')->getReturnReasons();

        return isset($reasons[$this->getReasonId()]) ? $reasons[$this->getReasonId()] : "";
    }

    public function checkReturnAvailability(){
        $prevRequests = $this->getCollection()
                ->addFieldToFilter('user_id', Mage::getSingleton('customer/session')->getCustomerId())
                ->addFieldToFilter('date', array('gt' => date("Y-m-d H:i:s", strtotime('-30 day'))));
        if(count($prevRequests)>=3){
            Mage::throwException(Mage::helper('core')->__('You cannot return more books at this time. For questions please contact us.'));
        }
        return true;
    }

    public function sendRequestEmail(){
        $sender = array(
            'name' => Mage::getStoreConfig('trans_email/ident_support/name'),
            'email' => Mage::getStoreConfig('trans_email/ident_support/email')
        );
        $recepientEmail = Mage::getStoreConfig('sales_email/returns_request/contact');
        $vars = array(
            'customer_name' => Mage::getSingleton('customer/session')->getCustomer()->getName(),
            'book_sku'      => $this->getProductSku(),
            'order_id'      => $this->getOrderId(),
            'comments'      => $this->getReason(),
            'reason'      => $this->getReasonLabel(),
        );
        $storeId = Mage::app()->getStore()->getId();
        Mage::getModel('core/email_template')
            ->sendTransactional('sales_email_returns_request_template', $sender, $recepientEmail, null, $vars, $storeId);
    }
}
