<?php

class Infortis_Infortis_Model_System_Config_Source_Design_Font_Google_Subset
{
	public function toOptionArray()
	{
		return array(
			array('value' => 'cyrillic',			'label' => Mage::helper('infortis')->__('Cyrillic')),
			array('value' => 'cyrillic-ext',		'label' => Mage::helper('infortis')->__('Cyrillic Extended')),
			array('value' => 'greek',				'label' => Mage::helper('infortis')->__('Greek')),
			array('value' => 'greek-ext',			'label' => Mage::helper('infortis')->__('Greek Extended')),
			array('value' => 'khmer',				'label' => Mage::helper('infortis')->__('Khmer')),
			array('value' => 'latin',				'label' => Mage::helper('infortis')->__('Latin')),
			array('value' => 'latin-ext',			'label' => Mage::helper('infortis')->__('Latin Extended')),
			array('value' => 'vietnamese',			'label' => Mage::helper('infortis')->__('Vietnamese')),
		);
	}
}