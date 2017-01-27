<?php

class Infortis_Infortis_Model_System_Config_Backend_Design_Color_Validatetransparent extends Mage_Core_Model_Config_Data
{
	public function save()
	{
		//Get the value from config
		$v = $this->getValue();
		if ($v == 'rgba(0, 0, 0, 0)')
		{
			$this->setValue('transparent');
			//Mage::getSingleton('adminhtml/session')->addNotice("value == rgba(0, 0, 0, 0)");
		}
		return parent::save();
	}
}
