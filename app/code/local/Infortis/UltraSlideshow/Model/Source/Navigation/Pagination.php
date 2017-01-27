<?php

class Infortis_UltraSlideshow_Model_Source_Navigation_Pagination
{
    public function toOptionArray()
    {
        return array(
			array('value' => '',					'label' => Mage::helper('ultraslideshow')->__('Disabled')),
			array('value' => 'slider-pagination1',	'label' => Mage::helper('ultraslideshow')->__('Style 1')),
			array('value' => 'slider-pagination2',	'label' => Mage::helper('ultraslideshow')->__('Style 2')),
        );
    }
}
