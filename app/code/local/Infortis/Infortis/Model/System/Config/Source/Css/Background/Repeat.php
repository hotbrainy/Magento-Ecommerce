<?php

class Infortis_Infortis_Model_System_Config_Source_Css_Background_Repeat
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'no-repeat',	'label' => Mage::helper('infortis')->__('no-repeat')),
            array('value' => 'repeat',		'label' => Mage::helper('infortis')->__('repeat')),
            array('value' => 'repeat-x',	'label' => Mage::helper('infortis')->__('repeat-x')),
			array('value' => 'repeat-y',	'label' => Mage::helper('infortis')->__('repeat-y'))
        );
    }
}