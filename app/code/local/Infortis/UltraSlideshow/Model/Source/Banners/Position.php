<?php

class Infortis_UltraSlideshow_Model_Source_Banners_Position
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'left',	'label' => Mage::helper('ultraslideshow')->__('Left')),
			array('value' => 'right',	'label' => Mage::helper('ultraslideshow')->__('Right'))
        );
    }
}
