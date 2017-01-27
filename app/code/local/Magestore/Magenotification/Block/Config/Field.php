<?php
class Magestore_Magenotification_Block_Config_Field
	extends Mage_Adminhtml_Block_System_Config_Form_Field
	implements Varien_Data_Form_Element_Renderer_Interface
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	$html = $element->getBold() ? '<strong>' : '';
    	$html.= $element->getValue();
    	$html.= $element->getBold() ? '</strong>' : '';
    	$html.= $element->getAfterElementHtml();
    	return $html;	
    } 
}