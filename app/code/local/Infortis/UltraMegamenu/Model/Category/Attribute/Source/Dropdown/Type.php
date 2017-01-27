<?php

class Infortis_UltraMegamenu_Model_Category_Attribute_Source_Dropdown_Type
	extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	protected $_options;

	/**
	 * Get list of types
	 */
	public function getAllOptions()
	{
		if (!$this->_options)
		{
			$this->_options = array(
				array('value' => 0,			'label' => ''),
				array('value' => 1,			'label' => 'Mega drop-down'),
				array('value' => 2,			'label' => 'Classic drop-down'),
				array('value' => 3,			'label' => 'Simple submenu (no drop-down)'),
			);
		}
		return $this->_options;
	}
}
