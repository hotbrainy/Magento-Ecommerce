<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Model_System_Config_Source_Attribute_Splashable extends Fishpig_AttributeSplash_Model_System_Config_Source_Attribute_Abstract
{
	/**
	 * Generate, cache and retrieve the collection
	 *
	 * @return
	 */
	public function getCollection()
	{
		if (is_null($this->_collection)) {
			$this->_collection = Mage::getResourceModel('eav/entity_attribute_collection')
				->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
				->addFieldToFilter('frontend_input', array('in' => array('select', 'multiselect')));
		
			$this->_collection->getSelect()
				->where('`main_table`.`source_model` IS NULL OR `main_table`.`source_model` IN (?)', array('', 'eav/entity_attribute_source_table'))
				->order('main_table.frontend_label ASC');
				
		}
		
		return $this->_collection;
	}
}
