<?php

class Infortis_Ultimo_Model_System_Config_Backend_Header_Leftcolunits extends Mage_Core_Model_Config_Data
{	
	public function _afterSave()
    {
		//Get the saved value
		$value = $this->getValue();
		
		//Get the value from config (previous value)
		$oldValue = $this->getOldValue();
		
		if ($value != $oldValue)
		{
			if (empty($value) || trim($value) === '')
			{
				Mage::getSingleton('adminhtml/session')->addNotice(
					Mage::helper('ultimo')->__('Left Column in the header has been disabled and will not be displayed in the header. IMPORTANT: note that any blocks assigned to the Left Column will also not be displayed.')
				);
			}
			else
			{
				Mage::getSingleton('adminhtml/session')->addNotice(
					Mage::helper('ultimo')->__('Width of the Left Column in the header has changed (previous value: %s). Note that sum of these columns has to be equal 12 grid units.', $oldValue)
				);
			}
		}
		
        return parent::_afterSave();
    }
}
