<?php

class Entangled_Custom_Block_Rewrite_Customer_Address_Renderer_Default extends Mage_Customer_Block_Address_Renderer_Default {

    public function render(Mage_Customer_Model_Address_Abstract $address, $format = null)
    {
        return str_replace("-,","",parent::render($address, $format));
    }
}