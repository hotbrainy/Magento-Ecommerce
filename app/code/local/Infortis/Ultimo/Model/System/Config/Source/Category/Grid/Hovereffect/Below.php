<?php

class Infortis_Ultimo_Model_System_Config_Source_Category_Grid_Hovereffect_Below
{
    public function toOptionArray()
    {
        return array(
            array('value' => '',     'label' => Mage::helper('infortis')->__('')),
			array('value' => '640',  'label' => Mage::helper('infortis')->__('640 px')),
			array('value' => '480',  'label' => Mage::helper('infortis')->__('480 px')),
			array('value' => '320',  'label' => Mage::helper('infortis')->__('320 px')),
        );
    }
}