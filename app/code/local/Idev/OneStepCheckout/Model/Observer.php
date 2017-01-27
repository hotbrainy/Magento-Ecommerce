<?php
class Idev_OneStepCheckout_Model_Observer extends Mage_Core_Model_Abstract
{
    public function initialize_checkout($observer)
    {
        $helper = Mage::helper('onestepcheckout/checkout');
    }
}