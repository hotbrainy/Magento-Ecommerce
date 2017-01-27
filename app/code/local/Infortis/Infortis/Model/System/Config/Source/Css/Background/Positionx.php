<?php

class Infortis_Infortis_Model_System_Config_Source_Css_Background_Positionx
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'left',	'label' => Mage::helper('infortis')->__('left')),
            array('value' => 'center',	'label' => Mage::helper('infortis')->__('center')),
            array('value' => 'right',	'label' => Mage::helper('infortis')->__('right'))
        );
    }
}