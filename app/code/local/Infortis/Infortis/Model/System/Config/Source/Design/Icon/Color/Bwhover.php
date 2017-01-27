<?php

class Infortis_Infortis_Model_System_Config_Source_Design_Icon_Color_Bwhover
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'b',		'label' => Mage::helper('infortis')->__('Black')),
            array('value' => 'w',		'label' => Mage::helper('infortis')->__('White')),
            array('value' => 'bw',		'label' => Mage::helper('infortis')->__('Black | White on hover')),
            array('value' => 'wb',		'label' => Mage::helper('infortis')->__('White | Black on hover')),
        );
    }
}