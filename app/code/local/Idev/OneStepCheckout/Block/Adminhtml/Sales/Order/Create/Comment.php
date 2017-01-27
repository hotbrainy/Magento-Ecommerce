<?php
class Idev_OneStepCheckout_Block_Adminhtml_Sales_Order_Create_Comment extends Mage_Adminhtml_Block_Sales_Order_Create_Comment
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
        $comment = $this->getQuote()->getOnestepcheckoutCustomercomment();
        $html = '';

        if ($this->isShowCustomerCommentEnabled()){
            $html .= '<label for="order-comment">' . Mage::helper('sales')->__('OneStepCheckout Order Comments') . '</label><br />';
            $html .= '<textarea style="width:98%; height:8em;" id="order-comment" name="onestepcheckout_customercomment" rows="2" cols="15">'.$comment.'</textarea>';
        }

        return $html;
    }

    public function isShowCustomerCommentEnabled(){
        return Mage::getStoreConfig('onestepcheckout/exclude_fields/enable_comments', $this->getQuote()->getStore());
    }

}
