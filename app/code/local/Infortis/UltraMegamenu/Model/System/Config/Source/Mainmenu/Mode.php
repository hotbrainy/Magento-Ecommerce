<?php

class Infortis_UltraMegamenu_Model_System_Config_Source_Mainmenu_Mode
{
    public function toOptionArray()
    {
		return array(
			array('value' => '1',	'label' => Mage::helper('ultramegamenu')->__('Drop-down')),
			array('value' => '0',	'label' => Mage::helper('ultramegamenu')->__('Drop-down/Mobile')),
			array('value' => '-1',	'label' => Mage::helper('ultramegamenu')->__('Mobile')),

        );
    }
}
