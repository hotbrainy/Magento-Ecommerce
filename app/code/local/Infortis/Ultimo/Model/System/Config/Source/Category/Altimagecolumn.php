<?php

class Infortis_Ultimo_Model_System_Config_Source_Category_AltImageColumn
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'label',			'label' => Mage::helper('ultimo')->__('Label')),
            array('value' => 'position',		'label' => Mage::helper('ultimo')->__('Sort Order'))
        );
    }
}