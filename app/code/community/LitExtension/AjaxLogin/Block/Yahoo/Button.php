<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Block_Yahoo_Button extends Mage_Core_Block_Template
{
    protected $client = null;
    protected $userInfo = null;

    protected function _construct() {
        parent::_construct();

        $this->client = Mage::getSingleton('ajaxlogin/yahoo_client');
        if(!($this->client->isEnabled())) {
            return;
        }

        $this->userInfo = Mage::registry('ajaxlogin_yahoo_userinfo');

        if(!($redirect = Mage::getSingleton('customer/session')->getBeforeAuthUrl())) {
            $redirect = Mage::helper('core/url')->getCurrentUrl();
        }

        // Redirect uri
        Mage::getSingleton('core/session')->setYahooRedirect($redirect);

        $this->setTemplate('le_ajaxlogin/ajaxlogin/yahoo/button.phtml');
    }

    protected function _getButtonUrl()
    {
        if(empty($this->userInfo)) {
            return $this->client->createAuthUrl();
        } else {
            return $this->getUrl('ajaxlogin/yahoo/disconnect');
        }
    }

}
