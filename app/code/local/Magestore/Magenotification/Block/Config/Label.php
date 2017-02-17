<?php
class Magestore_Magenotification_Block_Config_Label
	extends Varien_Data_Form_Element_Label
{
    public function getElementHtml()
    {
    	$html = $this->getBold() ? '<strong>' : '';
    	$html.= $this->getValue();
    	$html.= $this->getBold() ? '</strong>' : '';
    	$html.= $this->getAfterElementHtml();
    	return $html;
    }   
}