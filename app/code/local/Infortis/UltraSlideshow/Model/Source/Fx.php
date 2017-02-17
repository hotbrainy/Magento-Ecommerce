<?php
/**
 * @deprecated
 */

class Infortis_UltraSlideshow_Model_Source_Fx
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'slide',	'label' => Mage::helper('ultraslideshow')->__('Slide')),
			array('value' => 'fade',	'label' => Mage::helper('ultraslideshow')->__('Fade'))
        );
    }
}
