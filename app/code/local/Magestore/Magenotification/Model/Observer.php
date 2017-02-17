<?php

class Magestore_Magenotification_Model_Observer
{
	public function controllerActionPredispatch($observer)
	{
		try{
			Mage::getModel('magenotification/magenotification')->checkUpdate();
		}catch(Exception $e){
		
		}
	}
	
}