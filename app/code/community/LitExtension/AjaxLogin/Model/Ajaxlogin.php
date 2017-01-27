<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */


class LitExtension_AjaxLogin_Model_Ajaxlogin extends LitExtension_AjaxLogin_Model_Validator
{    
    public function _construct() 
    {
        parent::_construct();
        
        $this->setEmail($_POST['email']);
        $this->setSinglePassword($_POST['password']);
        
        if ($this->_result == '')
        {
            $this->loginUser();
        }
    }
    
    private function loginUser()
    {
        $session = Mage::getSingleton('customer/session');

        try
        {
            $session->login($this->_userEmail, $this->_userPassword);
            $customer = $session->getCustomer();
            
            $session->setCustomerAsLoggedIn($customer);
            
            $this->_result .= 'success';
        }
        catch(Exception $ex)
        {
            $this->_result .= 'wronglogin,';
        }
    }
    
    public function getResult()
    {
        return $this->_result;
    }
}

?>
