<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Rules_Editable extends Mage_Core_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
	    $valueName = $element->getValueName();

	    if ($valueName==='') {
	        $valueName = '...';
	    } else {
	        $valueName = Mage::helper('core/string')->truncate($valueName, 30);
	    }
	    if ($element->getShowAsText()) {
	        $html = ' <input type="hidden" class="hidden" id="'.$element->getHtmlId().'" name="'.$element->getName().'" value="'.$element->getValue().'"/> ';

	        $html.= htmlspecialchars($valueName).'&nbsp;';
	    } else {
    		$html = ' <span class="rule-param"' . ($element->getParamId() ? ' id="' . $element->getParamId() . '"' : '') . '>';

    		$html.= '<a href="javascript:void(0)" class="label">';

    		$html.= htmlspecialchars($valueName);

    		$html.= '</a><span class="element"> ';

    		$html.= $element->getElementHtml();

    		if ($element->getExplicitApply()) {
    		    $html.= ' <a href="javascript:void(0)" class="rule-param-apply"><img src="'.$this->getSkinUrl('images/rule_component_apply.gif').'" class="v-middle" alt="'.$this->__('Apply').'" title="'.$this->__('Apply').'" /></a> ';
    		}

    		$html.= '</span></span>&nbsp;';
	    }
		return $html;
	}
}