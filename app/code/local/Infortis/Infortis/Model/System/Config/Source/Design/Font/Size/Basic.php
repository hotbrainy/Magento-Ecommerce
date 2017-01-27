<?php

class Infortis_Infortis_Model_System_Config_Source_Design_Font_Size_Basic
{
	public function toOptionArray()
	{
		return array(
			array('value' => '12',		'label' => Mage::helper('infortis')->__('12 px')),
			array('value' => '13',		'label' => Mage::helper('infortis')->__('13 px')),
			array('value' => '14',		'label' => Mage::helper('infortis')->__('14 px')),
			array('value' => '15',		'label' => Mage::helper('infortis')->__('15 px')),
			array('value' => '16',		'label' => Mage::helper('infortis')->__('16 px')),
			//Old:
			//array('value' => '12px',	'label' => Mage::helper('infortis')->__('12 px')),
			//array('value' => '13px',	'label' => Mage::helper('infortis')->__('13 px')),
			//array('value' => '14px',	'label' => Mage::helper('infortis')->__('14 px')),
		);
	}
}