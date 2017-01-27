<?php

class Entangled_Returns_Helper_Data extends Mage_Core_Helper_Abstract {

    public function getReturnReasons(){
        return array(
            0 => "Found a better price elsewhere",
            1 => "Accidental Purchase",
            2 => "Unwanted Purchase",
            3 => "Quality Issues",
            4 => "Compatibility issues",
            5 => "Download issues",
            6 => "Offensive content",
            7 => "Other",
        );
    }
}