<?php

class Infortis_Infortis_Model_System_Config_Source_Css_Background_Positiony
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'top',		'label' => Mage::helper('infortis')->__('top')),
            array('value' => 'center',	'label' => Mage::helper('infortis')->__('center')),
            array('value' => 'bottom',	'label' => Mage::helper('infortis')->__('bottom'))
        );
    }
}