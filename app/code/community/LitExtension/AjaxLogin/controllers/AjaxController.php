<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        if (isset($_POST['ajax']))
        {
            if ($_POST['ajax'] == 'login' && Mage::helper('customer')->isLoggedIn() != true)
            {
                $login = Mage::getSingleton('ajaxlogin/ajaxlogin');
                echo $login->getResult();
            }
            elseif ($_POST['ajax'] == 'register' && Mage::helper('customer')->isLoggedIn() != true)
            {
                $register = Mage::getSingleton('ajaxlogin/ajaxregister');
                echo $register->getResult();
            }
        }
    }

    public function viewAction()
    {
    }
}

?>