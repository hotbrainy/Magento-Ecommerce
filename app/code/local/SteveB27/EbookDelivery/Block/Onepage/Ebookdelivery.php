<?php

class SteveB27_EbookDelivery_Block_Onepage_Ebookdelivery extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {    	
        $this->getCheckout()->setStepData('ebookdelivery', array(
            'label'     => Mage::helper('checkout')->__('Ebook Delivery'),
            'is_show'   => true
        ));
        
        parent::_construct();
    }
}