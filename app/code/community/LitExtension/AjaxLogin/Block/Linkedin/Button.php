<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Block_Linkedin_Button extends Mage_Core_Block_Template
{
    protected $client = null;
    protected $oauth2 = null;
    protected $userInfo = null;

    protected function _construct() {
        parent::_construct();

        $this->client = Mage::getSingleton('ajaxlogin/linkedin_client');
        if(!($this->client->isEnabled())) {
            return;
        }

        $this->userInfo = Mage::registry('ajaxlogin_linkedin_userinfo');

        // CSRF protection
        Mage::getSingleton('core/session')->setLinkedinCsrf($csrf = md5(uniqid(rand(), TRUE)));
        $this->client->setState($csrf);
        
        if(!($redirect = Mage::getSingleton('customer/session')->getBeforeAuthUrl())) {
            $redirect = Mage::helper('core/url')->getCurrentUrl();      
        }        
        
        // Redirect uri
        Mage::getSingleton('core/session')->setLinkedinRedirect($redirect);

        $this->setTemplate('le_ajaxlogin/ajaxlogin/linkedin/button.phtml');
    }

    protected function _getButtonUrl()
    {
        if(empty($this->userInfo)) {
            return $this->client->createAuthUrl();
        } else {
            return $this->getUrl('ajaxlogin/linkedin/disconnect');
        }
    }
}
