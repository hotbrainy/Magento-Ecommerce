<?php
/**
 * @project     AjaxLogin
 * @package LitExtension_AjaxLogin
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_AjaxLogin_Model_System_Config_Source_Position {

    public function toOptionArray() {
        return array(
            array('value' => 0, 'label' => Mage::helper('adminhtml')->__('Center')),
            array('value' => 1, 'label' => Mage::helper('adminhtml')->__('Below button')),
        );
    }

}
