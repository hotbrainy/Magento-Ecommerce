<?php

class Infortis_Ultimo_Model_System_Config_Source_Category_Grid_Size
{
	public function toOptionArray()
	{
		return array(
			array('value' => '',	'label' => Mage::helper('infortis')->__('Default')),
			array('value' => 's',	'label' => Mage::helper('infortis')->__('Size S')),
			array('value' => 'xs',	'label' => Mage::helper('infortis')->__('Size XS')),
		);
	}
}