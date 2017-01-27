<?php
/**
 * Created by PhpStorm.
 * User: riterrani
 * Date: 11/27/16
 * Time: 6:54 PM
 */ 
class Entangled_Custom_Block_Rewrite_Newsletter_Subscribe extends Mage_Newsletter_Block_Subscribe {

    public function getCurrentMail(){
        $session = Mage::getSingleton("customer/session");

        return $session->isLoggedIn() ? $session->getCustomer()->getData("email") : "";
    }

}