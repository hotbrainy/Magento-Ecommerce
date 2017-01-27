<?php

class Infortis_UltraSlideshow_Model_Source_Navigation_Pagination_Position
{
    public function toOptionArray()
    {
        return array(
			array('value' => 'pagination-pos-bottom-centered',		'label' => Mage::helper('ultraslideshow')->__('Bottom, centered')),
			array('value' => 'pagination-pos-bottom-right',			'label' => Mage::helper('ultraslideshow')->__('Bottom, right')),
			array('value' => 'pagination-pos-bottom-left',			'label' => Mage::helper('ultraslideshow')->__('Bottom, left')),
			array('value' => 'pagination-pos-over-bottom-centered',	'label' => Mage::helper('ultraslideshow')->__('Bottom, centered, over the slides')),
			array('value' => 'pagination-pos-over-bottom-right',	'label' => Mage::helper('ultraslideshow')->__('Bottom, right, over the slides')),
			array('value' => 'pagination-pos-over-bottom-left',		'label' => Mage::helper('ultraslideshow')->__('Bottom, left, over the slides')),
        );
    }
}
