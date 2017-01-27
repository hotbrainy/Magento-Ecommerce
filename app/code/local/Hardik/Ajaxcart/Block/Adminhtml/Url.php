<?php
/**
 * @category   Creativestyle
 * @package    Creativestyle_Varnish
 * @copyright  Copyright (c) 2011 creativestyle GmbH (http://www.creativestyle.de)
 * @author     Marek Zabrowarny / creativestyle GmbH <support@creativestyle.de>
 */

class Hardik_Ajaxcart_Block_Adminhtml_Url extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        $output = '<script type="text/javascript">//<![CDATA[' . "\n";
        $output .= '    var xml_form_template = \'' . str_replace("'", "\'", $this->_getRowEditHtml()) .'\';' . "\n";
        $output .= '//]]></script>' . "\n";
        $output .= '<input type="hidden" name="' . $this->getElement()->getName() . '" value="">';
        $output .= '<table id="xml_container" style="border-collapse:collapse;"><tbody>';
        $output .= $this->_getHeaderHtml();
        if ($this->getElement()->getData('value')) {
            foreach ($this->getElement()->getData('value/id') as $elementIndex => $elementName) {
                $output .= $this->_getRowHtml($elementIndex);
            }
        }
        $output .= '<tr><td colspan="2" style="padding: 4px 0;">';
        $output .= $this->_getAddButtonHtml();
        $output .= '</td></tr>';
        $output .= '</tbody></table>';
        return $output;
    }

    protected function _getHeaderHtml() {
        $output = '<tr>';
        $output .= '<th style="padding: 2px; text-align: center;">';
        $output .= Mage::helper('ajaxcart')->__('ID/Class selector of block to be updated');
        $output .= '</th>';
        $output .= '<th style="padding: 2px; text-align: center;">';
        $output .= Mage::helper('ajaxcart')->__('Layout Update Block name(should be same as in XML)');
        $output .= '</th>';
        $output .= '<th>&nbsp;</th>';
        $output .= '</tr>';
        return $output;
    }

    protected function _getRowHtml($index = 0) {
        $output = '<tr>';
        $output .= '<td style="padding: 2px 0;">';
        $output .= '<input type="text" class="required-entry input-text" style="margin-right:10px" name="' . $this->getElement()->getName() . '[id][]" value="' . $this->getElement()->getData('value/id/' . $index) . '" />';
        $output .= '</td>';
        $output .= '<td style="padding: 2px 0;">';
        $output .= '<input class="required-entry input-text" name="' . $this->getElement()->getName() . '[xml][]" value="'.$this->getElement()->getData('value/xml/' . $index) . '">';
        $output .= '</td>';
        $output .= '<td style="padding: 2px 4px;">';
        $output .= $this->_getRemoveButtonHtml();
        $output .= '</td>';
        $output .= '</tr>';
        return $output;
    }

    protected function _getRowEditHtml() {
        $output = '<tr>';
        $output .= '<td style="padding: 2px 0;">';
        $output .= '<input class="required-entry input-text" style="margin-right:10px" name="' . $this->getElement()->getName() . '[id][]" />';
        $output .= '</td>';
        $output .= '<td style="padding: 2px 0;">';
        $output .= '<input class="required-entry input-text" name="' . $this->getElement()->getName() . '[xml][]">';
        $output .= '</td>';
        $output .= '<td style="padding: 2px 4px;">';
        $output .= $this->_getRemoveButtonHtml();
        $output .= '</td>';
        $output .= '</tr>';
        return $output;
    }

    protected function _getAddButtonHtml() {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('add')
            ->setLabel($this->__('Add Layout Update Block'))
            ->setOnClick("Element.insert($(this).up('tr'), {before: xml_form_template})")
            ->toHtml();
    }

    protected function _getRemoveButtonHtml() {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('delete v-middle')
            ->setLabel($this->__('Delete'))
            ->setOnClick("Element.remove($(this).up('tr'))")
            ->toHtml();
    }
}
