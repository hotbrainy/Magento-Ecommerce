<?php

class Infortis_Ultimo_Model_System_Config_Source_Header_Position_PrimaryMenuContainer
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'menuContainer',		'label' => Mage::helper('ultimo')->__('Full Width Menu Container')),
			array('value' => 'primLeftCol',			'label' => Mage::helper('ultimo')->__('Primary, Left Column')),
			array('value' => 'primCentralCol',		'label' => Mage::helper('ultimo')->__('Primary, Central Column')),
			array('value' => 'primRightCol',		'label' => Mage::helper('ultimo')->__('Primary, Right Column')),
        );
    }
}