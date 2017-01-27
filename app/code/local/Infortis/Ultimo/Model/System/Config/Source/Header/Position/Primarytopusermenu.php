<?php

class Infortis_Ultimo_Model_System_Config_Source_Header_Position_PrimaryTopUserMenu
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'topLeft_1',			'label' => Mage::helper('ultimo')->__('Top, Left')),
			array('value' => 'topRight_1',			'label' => Mage::helper('ultimo')->__('Top, Right')),
			array('value' => 'primLeftCol',			'label' => Mage::helper('ultimo')->__('Primary, Left Column')),
			array('value' => 'primCentralCol',		'label' => Mage::helper('ultimo')->__('Primary, Central Column')),
			array('value' => 'primRightCol',		'label' => Mage::helper('ultimo')->__('Primary, Right Column')),
			array('value' => 'userMenu',			'label' => Mage::helper('ultimo')->__('Inside User Menu...')),
        );
    }
}
