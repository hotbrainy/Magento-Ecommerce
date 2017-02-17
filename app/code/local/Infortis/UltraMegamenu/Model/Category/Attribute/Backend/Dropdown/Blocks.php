<?php

class Infortis_UltraMegamenu_Model_Category_Attribute_Backend_Dropdown_Blocks 
	extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
	/**
	 * Before save method
	 *
	 * @param Varien_Object $object
	 * @return Mage_Eav_Model_Entity_Attribute_Backend_Abstract
	 */
	public function beforeSave($object)
	{
		$attributeCode = $this->getAttribute()->getAttributeCode();
		$attributeValue = $object->getData($attributeCode);
		$delimiter = Infortis_UltraMegamenu_Block_Category_Attribute_Helper_Dropdown_Blocks::DELIMITER;
		$maxBlocks = Infortis_UltraMegamenu_Block_Category_Attribute_Helper_Dropdown_Blocks::MAX_BLOCKS;

		if ($attributeValue)
		{
			//To make parsing simpler, replace value with empty string if all blocks are empty
			$exploded = explode($delimiter, $attributeValue);
			$allBlocksAreEmpty = TRUE;
			for ($i = 0; $i < $maxBlocks; $i++)
			{
				if (isset($exploded[$i]))
				{
					if (trim($exploded[$i]))
					{
						//Block is not empty after trimming
						$allBlocksAreEmpty = FALSE;
						break;
					}
				}
			}

			if ($allBlocksAreEmpty)
			{
				$object->setData($attributeCode, ''); //Set empty value
			}
		}

		return parent::beforeSave($object);
	}
}
