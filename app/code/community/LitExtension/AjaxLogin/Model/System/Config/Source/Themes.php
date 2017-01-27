<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Model_System_Config_Source_Themes {

    public function toOptionArray() {
        return array(
            array('value' => 'simple', 'label' => Mage::helper('adminhtml')->__('Simple')),
            array('value' => 'plastic', 'label' => Mage::helper('adminhtml')->__('Plastic')),
        );
    }

}
