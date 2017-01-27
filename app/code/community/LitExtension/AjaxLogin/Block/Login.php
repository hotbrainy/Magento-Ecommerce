<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Block_Login extends Mage_Core_Block_Template
{

    protected function _construct() {
        parent::_construct();

        $this->setTemplate('le_ajaxlogin/customer/form/login.phtml');
    }

}
