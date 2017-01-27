<?php
/**
 *    OneStepCheckout main block
 *    @author Jone Eide <mail@onestepcheckout.com>
 *    @copyright Jone Eide <mail@onestepcheckout.com>
 *
 */

class Idev_OneStepCheckout_Block_Register extends Mage_Checkout_Block_Onepage_Abstract    {

    public function show()
    {
        $lastOrderId = $this->getOnepage()->getCheckout()->getLastOrderId();
        $order = Mage::getModel('sales/order')->load($lastOrderId);
        $registration_mode = Mage::getStoreConfig('onestepcheckout/registration/registration_mode');

        if($lastOrderId && !$this->_isLoggedIn() && !$this->_isEmailRegistered($order->getCustomerEmail()) && $registration_mode == 'registration_success')    {
            return true;
        }
        return false;
    }

    protected function _isEmailRegistered($email)
    {
        $model = Mage::getModel('customer/customer');
        $model->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);

        if($model->getId() == NULL)    {
            return false;
        }

        return true;
    }

    protected function _isLoggedIn()
    {
        $helper = $this->helper('customer');
        if( $helper->isLoggedIn() )    {
            return true;
        }

        return false;

    }

    public function getOnepage()
    {
        return Mage::getSingleton('checkout/type_onepage');
    }
}
