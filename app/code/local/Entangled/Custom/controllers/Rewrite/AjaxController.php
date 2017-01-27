<?php
require_once 'LitExtension/AjaxLogin/controllers/AjaxController.php';

class Entangled_Custom_Rewrite_AjaxController extends LitExtension_AjaxLogin_AjaxController {

    /**
     * Retrieve core session model object
     *
     * @return Mage_Core_Model_Session
     */
    protected function _getCoreSession()
    {
        return Mage::getSingleton('core/session');
    }

    /**
     * Set wishlist request in core session
     */
    public function addToWishlistRequestAction(){
        $coreSession = $this->_getCoreSession();
        $wishlistProduct = $this->getRequest()->getParam('wishlist_product_id');
        $addToWishlistUrl = Mage::getUrl('wishlist/index/add',array('product'=>$wishlistProduct));
        $coreSession->setSocialLoginUrlReferer($addToWishlistUrl);
        if($coreSession->getLastUrl() == Mage::getUrl('cms/index/index')){
            $coreSession->setAfterWishlistRedirect(Mage::getUrl(''));
        }else{
            $coreSession->setAfterWishlistRedirect($coreSession->getLastUrl());
        }
        return true;
    }

}