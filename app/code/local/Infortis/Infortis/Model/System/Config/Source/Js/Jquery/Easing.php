<?php

class Infortis_Infortis_Model_System_Config_Source_Js_Jquery_Easing
{
    public function toOptionArray()
    {
        return array(
			//Ease in-out
			array('value' => 'easeInOutSine',	'label' => Mage::helper('infortis')->__('easeInOutSine')),
			array('value' => 'easeInOutQuad',	'label' => Mage::helper('infortis')->__('easeInOutQuad')),
			array('value' => 'easeInOutCubic',	'label' => Mage::helper('infortis')->__('easeInOutCubic')),
			array('value' => 'easeInOutQuart',	'label' => Mage::helper('infortis')->__('easeInOutQuart')),
			array('value' => 'easeInOutQuint',	'label' => Mage::helper('infortis')->__('easeInOutQuint')),
			array('value' => 'easeInOutExpo',	'label' => Mage::helper('infortis')->__('easeInOutExpo')),
			array('value' => 'easeInOutCirc',	'label' => Mage::helper('infortis')->__('easeInOutCirc')),
			array('value' => 'easeInOutElastic','label' => Mage::helper('infortis')->__('easeInOutElastic')),
			array('value' => 'easeInOutBack',	'label' => Mage::helper('infortis')->__('easeInOutBack')),
			array('value' => 'easeInOutBounce',	'label' => Mage::helper('infortis')->__('easeInOutBounce')),
			//Ease out
			array('value' => 'easeOutSine',		'label' => Mage::helper('infortis')->__('easeOutSine')),
			array('value' => 'easeOutQuad',		'label' => Mage::helper('infortis')->__('easeOutQuad')),
			array('value' => 'easeOutCubic',	'label' => Mage::helper('infortis')->__('easeOutCubic')),
			array('value' => 'easeOutQuart',	'label' => Mage::helper('infortis')->__('easeOutQuart')),
			array('value' => 'easeOutQuint',	'label' => Mage::helper('infortis')->__('easeOutQuint')),
			array('value' => 'easeOutExpo',		'label' => Mage::helper('infortis')->__('easeOutExpo')),
			array('value' => 'easeOutCirc',		'label' => Mage::helper('infortis')->__('easeOutCirc')),
			array('value' => 'easeOutElastic',	'label' => Mage::helper('infortis')->__('easeOutElastic')),
			array('value' => 'easeOutBack',		'label' => Mage::helper('infortis')->__('easeOutBack')),
			array('value' => 'easeOutBounce',	'label' => Mage::helper('infortis')->__('easeOutBounce')),
			//Ease in
			array('value' => 'easeInSine',		'label' => Mage::helper('infortis')->__('easeInSine')),
			array('value' => 'easeInQuad',		'label' => Mage::helper('infortis')->__('easeInQuad')),
			array('value' => 'easeInCubic',		'label' => Mage::helper('infortis')->__('easeInCubic')),
			array('value' => 'easeInQuart',		'label' => Mage::helper('infortis')->__('easeInQuart')),
			array('value' => 'easeInQuint',		'label' => Mage::helper('infortis')->__('easeInQuint')),
			array('value' => 'easeInExpo',		'label' => Mage::helper('infortis')->__('easeInExpo')),
			array('value' => 'easeInCirc',		'label' => Mage::helper('infortis')->__('easeInCirc')),
			array('value' => 'easeInElastic',	'label' => Mage::helper('infortis')->__('easeInElastic')),
			array('value' => 'easeInBack',		'label' => Mage::helper('infortis')->__('easeInBack')),
			array('value' => 'easeInBounce',	'label' => Mage::helper('infortis')->__('easeInBounce')),
			//No easing
			array('value' => '',				'label' => Mage::helper('infortis')->__('No easing'))
        );
    }
}
