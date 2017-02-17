<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Model_Resource_Page extends Fishpig_AttributeSplash_Model_Resource_Abstract
{
	/**
	 * Fields to be serialized before saving
	 * This applies to the filter fields
	 *
	 * @var array
	 */
     protected $_serializableFields = array(
     	'other' => array('a:0:{}', array()),
     );
     
	public function _construct()
	{
		$this->_init('attributeSplash/page', 'page_id');
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
		$select = parent::_getLoadSelect($field, $value, $object)
			->join(array('_option_table' => $this->getTable('eav/attribute_option')), '`_option_table`.`option_id` = `main_table`.`option_id`', '')
			->join(array('_attribute_table' => $this->getTable('eav/attribute')), '`_attribute_table`.`attribute_id`=`_option_table`.`attribute_id`', array('attribute_id', 'attribute_code', 'frontend_label'));
		
		return $select;
	}
	
	/**
	 * Retrieve the store table name
	 *
	 * @return string
	 */
	public function getStoreTable()
	{
		return $this->getTable('attributeSplash/page_store');
	}

	/**
	 * Retrieve the name of the unique field
	 *
	 * @return string
	 */
	public function getUniqueFieldName()
	{
		return 'option_id';	
	}
 
	/**
	 * Retrieve a collection of products associated with the splash page
	 *
	 * @return Mage_Catalog_Model_Resource_Eav_Resource_Product_Collection
	 */	
	public function getProductCollection(Fishpig_AttributeSplash_Model_Page $page)
	{	
		$collection = Mage::getResourceModel('catalog/product_collection')
			->setStoreId($page->getStoreId());

		$alias = $page->getAttributeCode() . '_index';

		$collection->getSelect()
			->join(
				array($alias => $this->getTable('catalog/product_index_eav')),
				"`{$alias}`.`entity_id` = `e`.`entity_id`"
				. $this->_getReadAdapter()->quoteInto(" AND `{$alias}`.`attribute_id` = ? ", $page->getAttributeId())
				. $this->_getReadAdapter()->quoteInto(" AND `{$alias}`.`store_id` = ? ", $page->getStoreId())
				. $this->_getReadAdapter()->quoteInto(" AND `{$alias}`.`value` = ?", $page->getOptionId()),
				''
			);

		return $collection;
	}
	
	/**
	 * Set required fields before saving model
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if (!$object->getData('store_ids')) {
			$object->setData('store_ids', array(Mage::app()->getStore(true)->getId()));
		}

		if ($object->getId()) {
			$object->getAttributeModel();

			$object->unsetData('attribute_id');
		}

		if (!$this->_pageIsUniqueToStores($object)) {
			throw new Exception('A page already exists for this attribute and store combination.');
		}

		return parent::_beforeSave($object);
	}
	
	/**
	 * Determine whether [ages scope if unique to store
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return bool
	 */
	protected function _pageIsUniqueToStores(Mage_Core_Model_Abstract $object)
	{
		if (Mage::app()->isSingleStoreMode() || !$object->hasStoreIds()) {
			$stores = array(Mage_Core_Model_App::ADMIN_STORE_ID);
		}
		else {
			$stores = (array)$object->getData('store_ids');
		}

		$select = $this->_getReadAdapter()
			->select()
			->from(array('main_table' => $this->getMainTable()), 'page_id')
			->join(array('_store' => $this->getStoreTable()), 'main_table.page_id = _store.page_id', '')
			->where('option_id=?', $object->getOptionId())
			->limit(1);
			
		if (count($stores) === 1) {
			$select->where('_store.store_id = ?', array_shift($stores));
		}
		else {
			$select->where('_store.store_id IN (?)', $stores);
		}

		if ($object->getId()) {
			$select->where('main_table.page_id <> ?', $object->getId());
		}

		return $this->_getWriteAdapter()->fetchOne($select) === false;
	}
	
	/**
	 * Auto-update splash group
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		parent::_afterSave($object);
		
		if (!$object->getSkipAutoCreateGroup()) {
			$this->updateSplashGroup($object);
		}
		
		return $this;
	}
	
	/**
	 * Check whether the attribute group exists
	 * If not, create the group
	 *
	 * @param Fishpig_AttributeSPlash_Model_Page $page
	 */
	public function updateSplashGroup(Fishpig_AttributeSplash_Model_Page $page)
	{
		if (!$page->getSplashGroup()) {
			$group = Mage::getModel('attributeSplash/group')
				->setAttributeId($page->getAttributeModel()->getId())
				->setDisplayName($page->getAttributeModel()->getFrontendLabel())
				->setStoreId(0)
				->setIsEnabled(1);

			try {
				$group->save();
			}
			catch (Exception $e) {
				Mage::helper('attributeSplash')->log($e->getMessage());
			}
		}

		return $this;
	}

	/**
	 * Retrieve the group associated with the splash page
	 * This will retrieve the most related group
	 * If there isn't a group for the same store, the admin group will be returned
	 *
	 * @param Fishpig_AttributeSplash_Model_Page $page
	 * @return Fishpig_AttributeSplash_Model_Group|false
	 */
	public function getSplashGroup(Fishpig_AttributeSplash_Model_Page $page)
	{
		$groups = Mage::getResourceModel('attributeSplash/group_collection')
			->addAttributeIdFilter($page->getAttributeModel()->getAttributeId())
			->addStoreFilter($page->getStoreId())
			->setCurPage(1)
			->setPageSize(1)
			->load();

		return count($groups) > 0
			? $groups->getFirstItem()
			: false;
	}

	/**
	 * After loading object get other values
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		parent::_afterLoad($object);
		
		$other = $object->getOther();
		
		if (is_array($other)) {
			foreach($other as $key => $value) {
				if (!$object->hasData($key)) {
					$object->setData($key, $value);
				}
			}
		}

		return $this;
	}
	
	/**
	 * Get the index table for pags
	 *
	 * @return string
	 */
	public function getIndexTable()
	{
		return $this->getTable('attributeSplash/page_index');
	}
	
	/**
	 * Given a page URL key and maybe a group url key and store id,
	 * return page and group IDS
	 *
	 * @param string $pageUrlKey
	 * @param string $groupUrlKey = null
	 * @param int $storeId = null
	 * @return false|array
	 */
	public function getPageAndGroupIdByUrlKeys($pageUrlKey, $groupUrlKey = null, $storeId = null)
	{
		if (is_null($storeId)) {
			$storeId = Mage::app()->getStore()->getId();
		}
		
		$select = $this->_getReadAdapter()
			->select()
				->from(array('_index' => $this->getIndexTable()), 'page_id')
				->where('store_id=?', $storeId);
		
		// Join page URL Key
		$select->join(
			array('_page' => $this->getMainTable()),
			$this->_getReadAdapter()->quoteInto('_index.page_id=_page.page_id AND _page.url_key= ?', $pageUrlKey),	
			''//array('page_url_key' => 'url_key')
		);
		
		$select->where('_page.is_enabled=?', 1);

		if (!is_null($groupUrlKey)) {
			// Join Attribute and Option tables
			$select->join(
				array('_option' => $this->getTable('eav/attribute_option')),
				'_page.option_id=_option.option_id',
				''
			)->join(
				array('_attribute' => $this->getTable('eav/attribute')),
				'_attribute.attribute_id=_option.attribute_id',
				''
			);
		
			// Join group URL Key
			$select->join(
				array('_group' => $this->getTable('attributeSplash/group')),
				'_group.attribute_id=_attribute.attribute_id',
				array('group_id')//array('group_url_key' => 'url_key', 'group_id')
			);		
			
			// Remove results with no group URL key
			$select->where('_group.url_key <> ?', '');
		}
		
		if ($results = $this->_getReadAdapter()->fetchAll($select)) {
			if (count($results) === 1) {
				return array_shift($results);
			}

			echo $select . '<br/><br/>';
			echo sprintf('<pre>%s</pre>', print_r($results, true));
			exit('more than 1 result.');
		}
		
		return false;
	}
}
