<?php

class Infortis_Ultimo_Model_System_Config_Source_Header_Position_All
{
    public function toOptionArray()
    {
    	/**
    	 * @deprecated
    	 */
		return array(
			array('value' => 'primLeftCol',			'label' => Mage::helper('ultimo')->__('Left Column')),
			array('value' => 'primCentralCol',		'label' => Mage::helper('ultimo')->__('Central Column')),
			array('value' => 'primRightCol',		'label' => Mage::helper('ultimo')->__('Right Column')),
			array('value' => 'userMenu',			'label' => Mage::helper('ultimo')->__('Inside User Menu...')),
        );
    }
}