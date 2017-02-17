<?php

class Hardik_Ajaxcart_Block_Adminhtml_Info extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

    protected function _getInfo($content) {
        $output = $this->_getStyle();
        $output .= '<div class="creativestyle-info">';
        $output .= $content;
        $output .= '</div>';
        return $output;
    }

    protected function _getStyle() {
        $content = '<style>';
        $content .= '.creativestyle-info { border: 1px solid #cccccc; background: #e7efef; margin-bottom: 10px; padding: 10px; height: auto; }';
        $content .= '.creativestyle-info .creativestyle-logo { float: right; padding: 5px; }';
        $content .= '.creativestyle-info .creativestyle-command { border: 1px solid #cccccc; background: #ffffff; padding: 15px; text-align: left; margin: 10px 0; font-weight: bold; }';
        $content .= '.creativestyle-info h3 { color: #ea7601; }';
        $content .= '.creativestyle-info h3 small { font-weight: normal; font-size: 80%; font-style: italic; }';
        $content .= '</style>';
        return $content;
    }

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $content = '<h3>' . $this->__('Ajaxcart documentation').'</h3>';
        return $this->_getInfo($content);
    }

}