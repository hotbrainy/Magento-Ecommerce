<?php

class Infortis_Infortis_Block_Adminhtml_System_Config_Form_Field_Color extends Mage_Adminhtml_Block_System_Config_Form_Field
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
		$jsPath = $this->getJsUrl('infortis/jquery/jquery-for-admin.min.js');
		$mcPath = $this->getJsUrl('infortis/jquery/plugins/mcolorpicker/');
		
		if (Mage::registry('jqueryLoaded') == false)
		{
			$html .= '
			<script type="text/javascript" src="'. $jsPath .'"></script>
			<script type="text/javascript">jQuery.noConflict();</script>
			';
			Mage::register('jqueryLoaded', 1);
        }
		if (Mage::registry('colorPickerLoaded') == false)
		{
			$html .= '
			<script type="text/javascript" src="'. $mcPath .'mcolorpicker.min.js"></script>
			<script type="text/javascript">
				jQuery.fn.mColorPicker.init.replace = false;
				jQuery.fn.mColorPicker.defaults.imageFolder = "'. $mcPath .'images/";
				jQuery.fn.mColorPicker.init.allowTransparency = true;
				jQuery.fn.mColorPicker.init.showLogo = false;
			</script>
            ';
			Mage::register('colorPickerLoaded', 1);
        }
		
		$html .= '
			<script type="text/javascript">
				jQuery(function($){
					$("#'. $element->getHtmlId() .'").attr("data-hex", true).width("250px").mColorPicker();
				});
			</script>
        ';
		
        return $html;
    }
}
