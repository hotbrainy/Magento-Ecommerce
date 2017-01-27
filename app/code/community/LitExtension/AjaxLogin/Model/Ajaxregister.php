<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Model_Ajaxregister extends LitExtension_AjaxLogin_Model_Validator
{
    public function _construct()
    {
        parent::_construct();

        $this->_result = '';
        $this->_userId = -1;

        $this->setEmail($_POST['email']);
        if ($this->isEmailExist()) {
            $this->_result .= 'emailisexist,';
        } elseif ($this->isnotEmail($this->_userEmail) == true) {
            $this->_result .= 'isnotemail,';
        } else {
            $this->_result = 'success';
        }

        if(Mage::app()->getRequest()->getParam('wishlist')){
            $coreSession = Mage::getSingleton('core/session');
            $wishlistProduct = Mage::app()->getRequest()->getParam('wishlist_product_id');
            $addToWishlistUrl = Mage::getUrl('wishlist/index/add',array('product'=>$wishlistProduct));
            $coreSession->setPostAuthRedirect($addToWishlistUrl);
            if($coreSession->getLastUrl() == Mage::getUrl('cms/index/index')){
                $coreSession->setAfterWishlistRedirect(Mage::getUrl(''));
            }else{
                $coreSession->setAfterWishlistRedirect($coreSession->getLastUrl());
            }
        }
    }
}

?>
