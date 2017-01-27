<?php

class Infortis_Infortis_Model_System_Config_Source_Design_Font_Family_Groupcustomgoogle
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'custom',
				  'label' => Mage::helper('infortis')->__('Custom...')),
			array('value' => 'google',
				  'label' => Mage::helper('infortis')->__('Google Fonts...')),
			
			array('value' => 'Arial, "Helvetica Neue", Helvetica, sans-serif',
				  'label' => Mage::helper('infortis')->__('Arial, "Helvetica Neue", Helvetica, sans-serif')),
			array('value' => 'Georgia, serif',
				  'label' => Mage::helper('infortis')->__('Georgia, serif')),
			array('value' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif',
				  'label' => Mage::helper('infortis')->__('"Lucida Sans Unicode", "Lucida Grande", sans-serif')),
			array('value' => '"Palatino Linotype", "Book Antiqua", Palatino, serif',
				  'label' => Mage::helper('infortis')->__('"Palatino Linotype", "Book Antiqua", Palatino, serif')),
			array('value' => 'Tahoma, Geneva, sans-serif',
				  'label' => Mage::helper('infortis')->__('Tahoma, Geneva, sans-serif')),
			array('value' => '"Trebuchet MS", Helvetica, sans-serif',
				  'label' => Mage::helper('infortis')->__('"Trebuchet MS", Helvetica, sans-serif')),
			array('value' => 'Verdana, Geneva, sans-serif',
				  'label' => Mage::helper('infortis')->__('Verdana, Geneva, sans-serif')),
        );
    }
}