<?php

class Infortis_Ultimo_Model_System_Config_Source_Navshadow
{
    public function toOptionArray()
    {
        return array(
            array('value' => '',                     'label' => Mage::helper('infortis')->__('None')),
			array('value' => 'inner-container',      'label' => Mage::helper('infortis')->__('Inner container')),
			array('value' => 'bar',                  'label' => Mage::helper('infortis')->__('Menu items')),
        );
    }
}