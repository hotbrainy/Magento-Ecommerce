<?php

class Infortis_Ultimo_Model_System_Config_Source_Header_Usermenu_Break_Position
{
    public function toOptionArray()
    {
		return array(
			array('value' => '0',	'label' => Mage::helper('ultimo')->__('No Additional Line Break')),
			array('value' => '30',	'label' => Mage::helper('ultimo')->__('Before Cart Drop-Down Block')),
			array('value' => '31',	'label' => Mage::helper('ultimo')->__('After Cart Drop-Down Block')),
			array('value' => '32',	'label' => Mage::helper('ultimo')->__('Before Compare Block')),
			array('value' => '33',	'label' => Mage::helper('ultimo')->__('After Compare Block')),
			array('value' => '34',	'label' => Mage::helper('ultimo')->__('Before Top Links')),
			array('value' => '35',	'label' => Mage::helper('ultimo')->__('After Top Links')),
        );
    }
}