<?php

class Infortis_UltraMegamenu_Model_Category_Attribute_Backend_Grid_Columns
	extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
	const DELIMITER = ';';

	/**
	 * Before save method
	 *
	 * @param Varien_Object $object
	 * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
	 */
	public function beforeSave($object)
	{
		$delimiter = ';';
		$maxColumns = 3;
		$gridUnitMax = 12;

		$attributeCode = $this->getAttribute()->getAttributeCode();
		$attributeValue = $object->getData($attributeCode);

		if ($attributeValue)
		{
			//To make parsing simpler, replace value with empty string if all units are 0
			$exploded = explode($delimiter, $attributeValue);
			$sum = 0;
			for ($i = 0; $i < $maxColumns; $i++)
			{
				if (isset($exploded[$i]))
				{
					$sum += intval($exploded[$i]);
				}
			}

			if ($sum === 0)
			{
				$object->setData($attributeCode, ''); //Set empty value
			}
		}

		return parent::beforeSave($object);
	}
}
