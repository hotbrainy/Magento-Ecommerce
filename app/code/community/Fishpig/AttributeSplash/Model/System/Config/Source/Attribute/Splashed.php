<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Model_System_Config_Source_Attribute_Splashed extends Fishpig_AttributeSplash_Model_System_Config_Source_Attribute_Splashable
{
	/**
	 * Generate, cache and retrieve the collection
	 *
	 * @return
	 */
	public function getCollection()
	{
		if (is_null($this->_collection)) {
			parent::getCollection()->getSelect()
				->distinct(true)
				->join(
					array('_splash_group' => Mage::getSingleton('core/resource')->getTableName('attributeSplash/group')),
					'_splash_group.attribute_id=main_table.attribute_id',
					''
				);
		}
		
		return $this->_collection;
	}
}
