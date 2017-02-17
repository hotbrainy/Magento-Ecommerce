<?php
class Idev_OneStepCheckout_Block_Adminhtml_Sales_Order_View_Comment extends Mage_Adminhtml_Block_Sales_Order_View_Items
{
    public function _toHtml(){
        $html = parent::_toHtml();
        $comment = $this->getCommentHtml();
        return $html.$comment;
    }

    /**
     * get comment from order and return as html formatted string
     *
     *@return string
     */
    public function getCommentHtml(){
        $comment = $this->getOrder()->getOnestepcheckoutCustomercomment();
        $feedback = $this->getOrder()->getOnestepcheckoutCustomerfeedback();
        $html = '';

        if ($this->isShowCustomerCommentEnabled() && $comment){
            $html .= '<div id="customer_comment" class="giftmessage-whole-order-container"><div class="entry-edit">';
            $html .= '<div class="entry-edit-head"><h4>'.$this->helper('onestepcheckout')->__('Customer Comment').'</h4></div>';
            $html .= '<fieldset>'.nl2br(Mage::helper('core')->escapeHtml($comment)).'</fieldset>';
            $html .= '</div></div>';
        }

        if($this->isShowCustomerFeedbackEnabled()){
            $html .= '<div id="customer_feedback" class="giftmessage-whole-order-container"><div class="entry-edit">';
            $html .= '<div class="entry-edit-head"><h4>'.$this->helper('onestepcheckout')->__('Customer Feedback').'</h4></div>';
            $html .= '<fieldset>'.nl2br(Mage::helper('core')->escapeHtml($feedback)).'</fieldset>';
            $html .= '</div></div>';
        }

        return $html;
    }

    public function isShowCustomerCommentEnabled(){
        return Mage::getStoreConfig('onestepcheckout/exclude_fields/enable_comments', $this->getOrder()->getStore());
    }

    public function isShowCustomerFeedbackEnabled(){
        return Mage::getStoreConfig('onestepcheckout/feedback/enable_feedback', $this->getOrder()->getStore());
    }
}
