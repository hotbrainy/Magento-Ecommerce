<?php

class Infortis_Ultimo_Model_System_Config_Source_Product_Position_All
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'primCol_1',		'label' => Mage::helper('ultimo')->__('Primary Column, Position 1')),
			array('value' => 'primCol_2',		'label' => Mage::helper('ultimo')->__('Primary Column, Position 2')),
			//array('value' => 'primCol_3',		'label' => Mage::helper('ultimo')->__('Primary Column, Position 3')),

			array('value' => 'secCol_1',		'label' => Mage::helper('ultimo')->__('Secondary Column, Position 1')),
			array('value' => 'secCol_2',		'label' => Mage::helper('ultimo')->__('Secondary Column, Position 2')),
			array('value' => 'secCol_3',		'label' => Mage::helper('ultimo')->__('Secondary Column, Position 3')),

			array('value' => 'lowerPrimCol_1',	'label' => Mage::helper('ultimo')->__('Lower Primary Column, Position 1')),
			array('value' => 'lowerPrimCol_2',	'label' => Mage::helper('ultimo')->__('Lower Primary Column, Position 2')),

			array('value' => 'lowerSecCol_2',	'label' => Mage::helper('ultimo')->__('Lower Secondary Column, Position 1')),
			//array('value' => 'lowerSecCol_2',	'label' => Mage::helper('ultimo')->__('Lower Secondary Column, Position 2')),
        );
    }
}