<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Model_Resource_Group extends Fishpig_AttributeSplash_Model_Resource_Abstract
{
	public function _construct()
	{
		$this->_init('attributeSplash/group', 'group_id');
	}

	/**
	 * Retrieve select object for load object data
	 * This gets the default select, plus the attribute id and code
	 *
	 * @param   string $field
	 * @param   mixed $value
	 * @return  Zend_Db_Select
	*/
	protected function _getLoadSelect($field, $value, $object)
	{
		return parent::_getLoadSelect($field, $value, $object)
			->join(
				array('_attribute_table' => $this->getTable('eav/attribute')),
				'`_attribute_table`.`attribute_id` = `main_table`.`attribute_id`',
				array('attribute_code', 'frontend_label')
			);
	}
	
	/**
	 * Retrieve the store table for the group model
	 *
	 * @return string
	 */
	public function getStoreTable()
	{
		return $this->getTable('attributeSplash/group_store');
	}

	/**
	 * Retrieve the name of the unique field
	 *
	 * @return string
	 */
	public function getUniqueFieldName()
	{
		return 'attribute_id';	
	}
	
	/**
	 * Retrieve the attribute model for the group
	 *
	 * @param Fishpig_AttributeSplash_Model_Group $group
	 * @return Mage_Eav_Model_Entity_Attribute
	 */
	public function getAttributeModel(Fishpig_AttributeSplash_Model_Group $group)
	{
		return $group->getAttributeId()
			? Mage::getModel('eav/entity_attribute')->load($group->getAttributeId())
			: false;
	}

	/**
	 * Retrieve a collection of splash pages that belong to this group
	 *
	 * @param Fishpig_AttributeSplash_Model_Group $group
	 * @return Fishpig_AttributeSplash_Model_Resource_Page_Collection
	 */
	public function getSplashPages(Fishpig_AttributeSplash_Model_Group $group)
	{
		$pages = Mage::getResourceModel('attributeSplash/page_collection')
			->addIsEnabledFilter();

		if ($group->getStoreId() > 0) {
			$pages->addStoreFilter($group->getStoreId());
		}
		else if (($storeId = Mage::app()->getStore()->getId()) > 0) {
			$pages->addStoreFilter($storeId);
		}
		
		 return $pages->addAttributeIdFilter($group->getAttributeId());
	}

	/**
	 * Get the index table name
	 *
	 * @return string
	 */
	public function getIndexTable()
	{
		return $this->getTable('attributeSplash/group_index');
	}
	
	/**
	 * Determine whether the group can be deleted
	 *
	 * @param Fishpig_AttributeSplash_Model_Group $group
	 * @return bool
	 */
	public function canDelete(Fishpig_AttributeSplash_Model_Group $group)
	{
		if (!$group->isGlobal()) {
			return true;
		}

		$select = $this->_getReadAdapter()->select()
			->from(array('main_table' => $this->getTable('eav/attribute_option')), 'option_id')
			->join(
				array('_splash' => $this->getTable('attributeSplash/page')),
				'_splash.option_id = main_table.option_id'
			)
			->where('main_table.attribute_id=?', $group->getAttributeModel()->getId())
			->limit(1);
			
			
		return $this->_getReadAdapter()->fetchOne($select) === false;
	}
}
