<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Rules_Newchild extends Mage_Core_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$html = '&nbsp;<span class="rule-param rule-param-new-child"' . ($element->getParamId() ? ' id="' . $element->getParamId() . '"' : '') . '>';
		$html.= '<a href="javascript:void(0)" class="label">';
		$html.= $element->getValueName();
		$html.= '</a><span class="element">';
		$html.= $element->getElementHtml();
		$html.= '</span></span>&nbsp;';
		return $html;
	}
}