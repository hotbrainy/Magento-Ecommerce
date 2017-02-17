<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Rules_Conditions implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
	    if ($element->getRule() && $element->getRule()->getConditions())
	    {
	       return $element->getRule()->getConditions()->asHtmlRecursive();
	    }
	    return '';
	}
}