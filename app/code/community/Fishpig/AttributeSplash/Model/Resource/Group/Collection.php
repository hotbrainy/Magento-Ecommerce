<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Model_Resource_Group_Collection extends Fishpig_AttributeSplash_Model_Resource_Collection_Abstract
{
	public function _construct()
	{
		$this->_init('attributeSplash/group');
		
		$this->_map['fields']['attribute_id'] = '_attribute_table.attribute_id';
		$this->_map['fields']['attribute_code'] = '_attribute_table.attribute_code';
		
		return parent::_construct();
	}

	/**
	 * Init collection select
	 *
	 * @return Mage_Core_Model_Resource_Collection_Abstract
	*/
	protected function _initSelect()
	{
		parent::_initSelect();
		
		$this->getSelect()->join(
				array('_attribute_table' => $this->getTable('eav/attribute')),
				'`_attribute_table`.`attribute_id` = `main_table`.`attribute_id`',
				array('attribute_code', 'frontend_label')
			);
		
		return $this;
	}
	
	/**
	 * Order the groups by name
	 *
	 * @return $this
	 */
	public function addOrderByName()
	{
		$this->getSelect()->order('main_table.display_name ASC');
		$this->getSelect()->order('_attribute_table.frontend_label ASC');
		
		return $this;
	}
}
