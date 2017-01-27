<?php
class Idev_OneStepCheckout_Model_Observers_PaypalExpress
{

    protected $_isactive = "";

    /**
     * Check if OSC is active
     *
     * @return int;
     */
    public function isActive()
    {
        if ($this->_isactive === "") {
            $this->_isactive = (int) Mage::getStoreConfig('onestepcheckout/general/rewrite_checkout_links');
        }
        return $this->_isactive;
    }

    /**
     * Skip  paypal review needs some variables
     * @param unknown $observer
     */
    public function skipPaypalPreview($observer) {
        if(!$this->isActive()) {
            return;
        }

        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if(is_object($quote) && is_object($quote->getPayment())){
            //we need to fake the variable to bypass paypal_express sanity check for skipping the review
            $quote->getPayment()
            ->setAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_BUTTON, 0);
        }
    }

    /**
     * We need to agree all terms
     * @param unknown $observer
     */
    public function agreePaypalTerms($observer) {
        if(!$this->isActive()) {
            return;
        }
        $controller = $observer->getEvent()->getControllerAction();
        //see if there are required terms
        $requiredAgreements = Mage::helper('checkout')->getRequiredAgreementIds();
        if($requiredAgreements){
            $postThis = array();
            foreach ($requiredAgreements as $agreement) {
                $postThis[$agreement] = 'bypass';
            }
            //add to post as this is validated before order placement from post variables
            $controller->getRequest()->setPost('agreement', $postThis);
        }
    }
}
