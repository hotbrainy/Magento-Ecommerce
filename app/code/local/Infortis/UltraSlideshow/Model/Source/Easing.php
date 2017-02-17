<?php
/**
 * @deprecated
 */

class Infortis_UltraSlideshow_Model_Source_Easing
{
    public function toOptionArray()
    {
        return array(
			//Ease in-out
			array('value' => 'easeInOutSine',	'label' => Mage::helper('ultraslideshow')->__('easeInOutSine')),
			array('value' => 'easeInOutQuad',	'label' => Mage::helper('ultraslideshow')->__('easeInOutQuad')),
			array('value' => 'easeInOutCubic',	'label' => Mage::helper('ultraslideshow')->__('easeInOutCubic')),
			array('value' => 'easeInOutQuart',	'label' => Mage::helper('ultraslideshow')->__('easeInOutQuart')),
			array('value' => 'easeInOutQuint',	'label' => Mage::helper('ultraslideshow')->__('easeInOutQuint')),
			array('value' => 'easeInOutExpo',	'label' => Mage::helper('ultraslideshow')->__('easeInOutExpo')),
			array('value' => 'easeInOutCirc',	'label' => Mage::helper('ultraslideshow')->__('easeInOutCirc')),
			array('value' => 'easeInOutElastic','label' => Mage::helper('ultraslideshow')->__('easeInOutElastic')),
			array('value' => 'easeInOutBack',	'label' => Mage::helper('ultraslideshow')->__('easeInOutBack')),
			array('value' => 'easeInOutBounce',	'label' => Mage::helper('ultraslideshow')->__('easeInOutBounce')),
			//Ease out
			array('value' => 'easeOutSine',		'label' => Mage::helper('ultraslideshow')->__('easeOutSine')),
			array('value' => 'easeOutQuad',		'label' => Mage::helper('ultraslideshow')->__('easeOutQuad')),
			array('value' => 'easeOutCubic',	'label' => Mage::helper('ultraslideshow')->__('easeOutCubic')),
			array('value' => 'easeOutQuart',	'label' => Mage::helper('ultraslideshow')->__('easeOutQuart')),
			array('value' => 'easeOutQuint',	'label' => Mage::helper('ultraslideshow')->__('easeOutQuint')),
			array('value' => 'easeOutExpo',		'label' => Mage::helper('ultraslideshow')->__('easeOutExpo')),
			array('value' => 'easeOutCirc',		'label' => Mage::helper('ultraslideshow')->__('easeOutCirc')),
			array('value' => 'easeOutElastic',	'label' => Mage::helper('ultraslideshow')->__('easeOutElastic')),
			array('value' => 'easeOutBack',		'label' => Mage::helper('ultraslideshow')->__('easeOutBack')),
			array('value' => 'easeOutBounce',	'label' => Mage::helper('ultraslideshow')->__('easeOutBounce')),
			//Ease in
			array('value' => 'easeInSine',		'label' => Mage::helper('ultraslideshow')->__('easeInSine')),
			array('value' => 'easeInQuad',		'label' => Mage::helper('ultraslideshow')->__('easeInQuad')),
			array('value' => 'easeInCubic',		'label' => Mage::helper('ultraslideshow')->__('easeInCubic')),
			array('value' => 'easeInQuart',		'label' => Mage::helper('ultraslideshow')->__('easeInQuart')),
			array('value' => 'easeInQuint',		'label' => Mage::helper('ultraslideshow')->__('easeInQuint')),
			array('value' => 'easeInExpo',		'label' => Mage::helper('ultraslideshow')->__('easeInExpo')),
			array('value' => 'easeInCirc',		'label' => Mage::helper('ultraslideshow')->__('easeInCirc')),
			array('value' => 'easeInElastic',	'label' => Mage::helper('ultraslideshow')->__('easeInElastic')),
			array('value' => 'easeInBack',		'label' => Mage::helper('ultraslideshow')->__('easeInBack')),
			array('value' => 'easeInBounce',	'label' => Mage::helper('ultraslideshow')->__('easeInBounce')),
			//No easing
			array('value' => 'none',			'label' => Mage::helper('ultraslideshow')->__('Disabled'))
        );
    }
}
