<?php
class Idev_OneStepCheckout_Block_Fields extends Idev_OneStepCheckout_Block_Checkout
{
    public function _construct(){
        $this->setSubTemplate(true);
        parent::_construct();
    }
}