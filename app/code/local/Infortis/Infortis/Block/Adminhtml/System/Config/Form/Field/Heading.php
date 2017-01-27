<?php
/**
 * Renderer for sub-heading in fieldset
 *
 */
class Infortis_Infortis_Block_Adminhtml_System_Config_Form_Field_Heading
	extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
	/**
	 * Render element html
	 *
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
	 */
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$useContainerId = $element->getData('use_container_id');
		return sprintf(
			'<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5" style="max-width:580px;"><h4 id="%s">%s</h4><p class="subheading-note" style="font-size:11px;font-style:italic;color:#999;"><span>%s</span></p></td></tr>',
			$element->getHtmlId(), $element->getHtmlId(), $element->getLabel(), $element->getComment()
		);

		//Original:
		/*return sprintf('<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5"><h2 id="%s">%s</h2></td></tr>',
			$element->getHtmlId(), $element->getHtmlId(), $element->getLabel()
		);*/
	}
}
