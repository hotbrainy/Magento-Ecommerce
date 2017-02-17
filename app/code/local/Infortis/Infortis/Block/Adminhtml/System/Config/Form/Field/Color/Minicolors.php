<?php

class Infortis_Infortis_Block_Adminhtml_System_Config_Form_Field_Color_Minicolors extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Add color picker
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return String
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $element->getElementHtml(); //Default HTML

        if(Mage::registry('colorPickerFirstUse') == false)
		{
			$html .= '
			<script type="text/javascript" src="'. $this->getJsUrl('infortis/jquery/jquery-for-admin.min.js') .'"></script>
			<script type="text/javascript" src="'. $this->getJsUrl('infortis/jquery/plugins/minicolors/jquery.minicolors.min.js') .'"></script>
			<script type="text/javascript">jQuery.noConflict();</script>
            <link type="text/css" rel="stylesheet" href="'. $this->getJsUrl('infortis/jquery/plugins/minicolors/jquery.minicolors.css') .'" />
            ';
			
			Mage::register('colorPickerFirstUse', 1);
        }
		
		$html .= '
			<script type="text/javascript">
				jQuery(function($){
					$("#'. $element->getHtmlId() .'").miniColors();
				});
			</script>
        ';
		
        return $html;
    }
}
