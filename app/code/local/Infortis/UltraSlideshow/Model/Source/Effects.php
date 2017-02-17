<?php

class Infortis_UltraSlideshow_Model_Source_Effects
{
	public function toOptionArray()
	{
		return array(
			array('value' => '',				'label' => Mage::helper('ultraslideshow')->__(' ')),
			array('value' => 'fade',			'label' => Mage::helper('ultraslideshow')->__('fade')),
			array('value' => 'backSlide',		'label' => Mage::helper('ultraslideshow')->__('backSlide')),
			array('value' => 'goDown',			'label' => Mage::helper('ultraslideshow')->__('goDown')),
			array('value' => 'fadeUp',			'label' => Mage::helper('ultraslideshow')->__('fadeUp')),
		);
	}
}
