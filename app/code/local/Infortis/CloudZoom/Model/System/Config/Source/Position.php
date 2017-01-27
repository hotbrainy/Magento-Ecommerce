<?php

class Infortis_CloudZoom_Model_System_Config_Source_Position
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'inside',		'label' => Mage::helper('infortis_cloudzoom')->__('Inside')),
			array('value' => 'right',		'label' => Mage::helper('infortis_cloudzoom')->__('Right')),
			array('value' => 'left',		'label' => Mage::helper('infortis_cloudzoom')->__('Left')),
			array('value' => 'top',			'label' => Mage::helper('infortis_cloudzoom')->__('Top')),
			array('value' => 'bottom',		'label' => Mage::helper('infortis_cloudzoom')->__('Bottom'))
        );
    }
}
