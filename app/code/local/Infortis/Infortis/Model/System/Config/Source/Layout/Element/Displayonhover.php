<?php

class Infortis_Infortis_Model_System_Config_Source_Layout_Element_Displayonhover
{
    public function toOptionArray()
    {
		return array(
			array('value' => 0, 'label' => Mage::helper('infortis')->__('Don\'t Display')),
            array('value' => 1, 'label' => Mage::helper('infortis')->__('Display On Hover')),
            array('value' => 2, 'label' => Mage::helper('infortis')->__('Display'))
        );
    }
}