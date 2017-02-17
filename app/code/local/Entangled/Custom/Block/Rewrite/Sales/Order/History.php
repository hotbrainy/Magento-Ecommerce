<?php
/**
 * Created by PhpStorm.
 * User: riterrani
 * Date: 11/7/16
 * Time: 2:21 PM
 */ 
class Entangled_Custom_Block_Rewrite_Sales_Order_History extends Mage_Sales_Block_Order_History {

    protected function _prepareLayout()
    {
        call_user_func(array(get_parent_class(get_parent_class($this)), '_prepareLayout'));

        $this->getOrders()->load();
        return $this;
    }

}