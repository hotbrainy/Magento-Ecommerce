<?php
class Idev_OneStepCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function checkValid($observer)
    {
        $layout = Mage::app()->getLayout();
        $content = $layout->getBlock('content');
        $block = $layout->createBlock('onestepcheckout/valid');
        $content->insert($block);
    }

    public function setCustomerComment($observer)
    {
        $enableComments = Mage::getStoreConfig('onestepcheckout/exclude_fields/enable_comments');
        $enableCommentsDefault = Mage::getStoreConfig('onestepcheckout/exclude_fields/enable_comments_default');
        $enableFeedback = Mage::getStoreConfig('onestepcheckout/feedback/enable_feedback');
        $orderComment = $this->_getRequest()->getPost('onestepcheckout_comments');
        $orderComment = trim($orderComment);

        if($enableComments && $enableCommentsDefault) {
            if ($orderComment != ""){
                $observer->getEvent()->getOrder()->setState($observer->getEvent()->getOrder()->getStatus(), true, Mage::helper('core')->escapeHtml($orderComment), false );
            }
        }
    }

    public function setCustomerCommentAdmin($observer)
    {

        $enableComments = Mage::getStoreConfig('onestepcheckout/exclude_fields/enable_comments');
        $enableCommentsDefault = Mage::getStoreConfig('onestepcheckout/exclude_fields/enable_comments_default');
        $enableFeedback = Mage::getStoreConfig('onestepcheckout/feedback/enable_feedback');
        $orderComment = $this->_getRequest()->getPost('onestepcheckout_customercomment');
        $orderComment = trim($orderComment);

        if($enableComments && !$enableCommentsDefault && $orderComment) {
            Mage::getSingleton('adminhtml/sales_order_create')->getQuote()->setOnestepcheckoutCustomercomment(Mage::helper('core')->escapeHtml($orderComment))->save();
        }
    }

    public function isRewriteCheckoutLinksEnabled()
    {
        return Mage::getStoreConfig('onestepcheckout/general/rewrite_checkout_links');
    }

    /**
     * If we are using enterprise wersion or not
     * @return int
     */
    public function isEnterprise(){
        return (int)is_object(Mage::getConfig()->getNode('global/models/enterprise_enterprise'));
    }

    /**
     * If we have ee_rewards enabled or not
     * @return int
     */
    public function hasEeRewards(){
        return (int)is_object(Mage::getConfig()->getNode('global/models/enterprise_reward'));
    }

    /**
     * If we have ee_customerbalance enabled or not
     * @return int
     */
    public function hasEeCustomerbalanace(){
        return (int)is_object(Mage::getConfig()->getNode('global/models/enterprise_customerbalance'));
    }

    /**
     * If we have ee_giftcard enabled or not
     * @return int
     */
    public function hasEeGiftcards(){
        return (int)is_object(Mage::getConfig()->getNode('global/models/enterprise_giftcard'));
    }

    /**
     * Encode the mixed $valueToEncode into the JSON format
     *
     * @param mixed $valueToEncode
     * @param  boolean $cycleCheck Optional; whether or not to check for object recursion; off by default
     * @param  array $options Additional options used during encoding
     * @return string
     */
    public function jsonEncode($valueToEncode, $cycleCheck = false, $options = array())
    {
        $json = Zend_Json::encode($valueToEncode, $cycleCheck, $options);
        /* @var $inline Mage_Core_Model_Translate_Inline */
        $inline = Mage::getSingleton('core/translate_inline');
        if ($inline->isAllowed()) {
            $inline->setIsJson(true);
            $inline->processResponseBody($json);
            $inline->setIsJson(false);
        }

        return $json;
    }

    /**
     * Check if value is only -
     * @param mixed $value
     */
    public function clearDash($value = null){
        if($value == '-'){
            return '';
        }
        if(method_exists(Mage::helper('core'), 'escapeHtml')){
            return Mage::helper('core')->escapeHtml($value);
        } else {
            //backwards compatibility with < 1.4.1.*
            return Mage::helper('core')->htmlEscape($value);
        }
    }
    /**
     * Check if persistent cart feature is present (for older Magento versions it is not)
     */
    public function getPersistentHelper(){
        $isPersistent = false;
        if(is_object(Mage::getConfig()->getNode('global/models/persistent'))){
            $isPersistent = Mage::helper('persistent/session');
        }
        return $isPersistent;
    }


    /**
     * Check if quote has errors
     * @return boolean
     */
    public function checkQuoteErrors($quote){
        $hasErrors = false;
        if (!$quote->hasItems() || $quote->getHasError()) {
            foreach($quote->getErrors() as $error){
                Mage::getSingleton('core/session')->addError(Mage::helper('core')->escapeHtml($error->getCode()));
            }
            $hasErrors = true;
        }
        if (!$quote->validateMinimumAmount()) {
            $error = Mage::getStoreConfig('sales/minimum_order/error_message');
            Mage::getSingleton('core/session')->addError($error);
            $hasErrors = true;
        }
        return $hasErrors;
    }

}
