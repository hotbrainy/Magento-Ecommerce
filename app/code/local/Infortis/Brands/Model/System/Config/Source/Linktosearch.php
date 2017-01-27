<?php

class Infortis_Brands_Model_System_Config_Source_Linktosearch
{
	public function toOptionArray()
	{
		return array(
			array('value' => 3,				'label' => Mage::helper('infortis')->__('-- No Link --')),
			array('value' => 1,				'label' => Mage::helper('infortis')->__('Quick Search Results')),
			array('value' => 2,				'label' => Mage::helper('infortis')->__('Advanced Search Results')),
			array('value' => 0,				'label' => Mage::helper('infortis')->__('Custom Page (more options...)')),
		);
	}
}