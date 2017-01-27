<?php
/**
 * MageWorx
 * All Extension
 *
 * @category   MageWorx
 * @package    MageWorx_All
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_All_Block_System_Config_Form_Fieldset_Mageworx_Abstract 	extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected function _getFooterHtml($element)
    {
        $html = parent::_getFooterHtml($element);
        $html .= Mage::helper('adminhtml/js')->getScript("
            $$('td.form-buttons')[0].update('');
            $('{$element->getHtmlId()}' + '-head').setStyle('background: none;');
            $('{$element->getHtmlId()}' + '-head').writeAttribute('onclick', 'return false;');
            $('{$element->getHtmlId()}').show();
        ");
        return $html;
    }
}
