<?php
class Idev_OneStepCheckout_Block_Checkout_Onepage_Link extends Mage_Checkout_Block_Onepage_Link
{
    public function getCheckoutUrl()
    {
        if (!$this->helper('onestepcheckout')->isRewriteCheckoutLinksEnabled()){
            return parent::getCheckoutUrl();
        }
        return $this->getUrl('onestepcheckout', array('_secure'=>true));
    }
}
