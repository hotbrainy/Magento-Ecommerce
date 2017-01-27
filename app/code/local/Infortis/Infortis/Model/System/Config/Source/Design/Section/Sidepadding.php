<?php

class Infortis_Infortis_Model_System_Config_Source_Design_Section_SidePadding
{
	public function toOptionArray()
	{
		return array(
			//If no value selected, use default side padding of the page
			array('value' => '',				'label' => Mage::helper('infortis')->__('Use Default')),
			//No side padding
			array('value' => 'expanded',		'label' => Mage::helper('infortis')->__('No Side Padding')),
			//Full-width inner container
			array('value' => 'full',			'label' => Mage::helper('infortis')->__('Full Width')),
			//Full-width inner container, no side padding
			array('value' => 'full-expanded',	'label' => Mage::helper('infortis')->__('Full Width, No Side Padding')),
		);
	}
}