<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
class Amasty_Shopby_Model_Source_Url_SortMode extends Varien_Object
{
	const MODE_CODE     = 1;
	const MODE_POSITION = 2;

	public function toOptionArray()
	{
		$hlp = Mage::helper('amshopby');
		return array(
			array('value' => self::MODE_CODE, 'label' => $hlp->__('Attribute Code')),
			array('value' => self::MODE_POSITION, 'label' => $hlp->__('Attribute Position')),
		);
	}
}