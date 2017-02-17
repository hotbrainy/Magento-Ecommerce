<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_AttributeSplash_Model_Resource_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
	/**
	 * Retrieve the name of the unique field
	 *
	 * @return string
	 */
	abstract public function getUniqueFieldName();
	
	/**
	 * Retreieve the name of the index table
	 *
	 * @return string
	 */
	abstract public function getIndexTable();
	
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
		$select = $this->_getReadAdapter()->select()
			->from(array('main_table' => $this->getMainTable()))
			->where("`main_table`.`{$field}` = ?", $value)
			->limit(1);
			
		$adminId = Mage_Core_Model_App::ADMIN_STORE_ID;
		
		$storeId = $object->getStoreId();
		
		if ($storeId !== $adminId) {
			$cond = $this->_getReadAdapter()->quoteInto(
				'`store`.`' . $this->getIdFieldName() . '` = `main_table`.`' . $this->getIdFieldName() . '` AND `store`.`store_id` IN (?)', array($adminId, $storeId)
			);
			
			$select->join(array('store' => $this->getStoreTable()), $cond, '')
				->order('store.store_id DESC');
		}

		return $select;
	}

	/**
	 * Get store ids to which specified item is assigned
	 *
	 * @param int $id
	 * @return array
	*/
	public function lookupStoreIds($objectId)
	{
		$select = $this->_getReadAdapter()->select()
			->from($this->getStoreTable(), 'store_id')
			->where($this->getIdFieldName() . ' = ?', (int)$objectId);
	
		return $this->_getReadAdapter()->fetchCol($select);
	}
	
	/**
	 * Determine whether the current store is the Admin store
	 *
	 * @return bool
	 */
	public function isAdmin()
	{
		return (int)Mage::app()->getStore()->getId() === Mage_Core_Model_App::ADMIN_STORE_ID;
	}
		
	/**
	 * Set required fields before saving model
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if (!$object->getDisplayName()) {
			if (!$object->getFrontendLabel()) {
				throw new Exception(Mage::helper('attributeSplash')->__('Splash object must have a name'));
			}
			else {
				$object->setDisplayName($object->getFrontendLabel());
			}
		}
		
		if ($object->getCategoryId()) {
			$category = Mage::getModel('catalog/category')->load($object->getCategoryId());
			
			if (!$category->getId()) {
				$object->setCategoryId(null);
			}
		}
		else {
			$object->setCategoryId(null);
		}
		
		if (!$object->getUrlKey()) {
			$object->setUrlKey($object->getname());
		}
		
		$object->setUrlKey($this->formatUrlKey($object->getUrlKey()));
		
		$object->setUpdatedAt(now());
		
		if (!$object->getCreatedAt()) {
			$object->setCreatedAt(now());
		}
		
		if (is_array($object->getCustomFields())) {
			$customFields = array();
			
			foreach($object->getCustomFields() as $field => $value) {
				if (trim($value) !== '') {
					$customFields[$field] = $value;
				}
			}
			
			if (count($customFields) > 0) {
				$object->setCustomFields(serialize($customFields));
			}
			else {
				$object->setCustomFIelds('');
			}
		}
		
		return parent::_beforeSave($object);
	}

	/**
	 * Set store data after saving model
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */	
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		if ($object->getId()) {
			$oldStores = $this->lookupStoreIds($object->getId());
			$newStores = (array)$object->getStoreIds();
	
			if (empty($newStores)) {
				$newStores = (array)$object->getStoreId();
			}
	
			$table  = $this->getStoreTable();
			$insert = array_diff($newStores, $oldStores);
			$delete = array_diff($oldStores, $newStores);
			
			if ($delete) {
				$this->_getWriteAdapter()->delete($table, array($this->getIdFieldName() . ' = ?' => (int) $object->getId(), 'store_id IN (?)' => $delete));
			}
			
			if ($insert) {
				$data = array();
			
				foreach ($insert as $storeId) {
					$data[] = array(
						$this->getIdFieldName()  => (int) $object->getId(),
						'store_id' => (int) $storeId
					);
				}

				$this->_getWriteAdapter()->insertMultiple($table, $data);
			}
			
			if (!$object->getSkipReindex()) {
				$object->getResource()->reindexAll();
			}
		}
	}

	/**
	 * Load store data after loading model
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		if ($object->getId()) {
			$storeIds = $this->lookupStoreIds($object->getId());
			$object->setData('store_ids', $storeIds);			
			
			if (!$this->isAdmin()) {
				$object->setStoreId(Mage::app()->getStore(true)->getId());
			}

			if ($object->getCustomFields()) {
				$customFields = @unserialize($object->getCustomFields());
				
				if (is_array($customFields)) {
					foreach($customFields as $field => $value) {
						$object->setData($field, $value);	
					}
				}
			}
		}
		
		return parent::_afterLoad($object);
	}

	/**
	 * Reindex all
	 *
	 * @return $this
	 */
	public function reindexAll()
	{
		$stores = Mage::getResourceModel('core/store_collection')->load();
		
		foreach($stores as $store) {
			$this->reindexAllByStoreId($store->getId());
		}
		
		return $this;
	}
	
	/**
	 * Reindex all by store ID
	 *
	 * @param int $storeId
	 * @return $this
	 */
	public function reindexAllByStoreId($storeId)
	{
		$this->_getWriteAdapter()->delete($this->getIndexTable(), $this->_getWriteAdapter()->quoteInto('store_id=?', $storeId));
			
		$subselect = $this->_getReadAdapter()
			->select()
			->from(array('main_table' => $this->getMainTable()), array($this->getIdFieldName(), $this->getUniqueFieldName()))
			->join(
				array('_store' => $this->getStoreTable()),
				'_store.' . $this->getIdFieldName() . '=main_table.' . $this->getIdFieldName(),
				''
			)
			->where('_store.store_id IN (?)', array($storeId, 0))
			->order('_store.store_id DESC');

		$select = $this->_getReadAdapter()->select()
			->from(array('main_table' => new Zend_Db_Expr('(' . (string)$subselect . ')')), $this->getIdFieldName())
			->columns(array('store_id' => new Zend_Db_Expr("'" . $storeId . "'")))
			->group($this->getUniqueFieldName());
		
		if ($objectIds = $this->_getReadAdapter()->fetchAll($select)) {
			$this->_getWriteAdapter()->insertMultiple($this->getIndexTable(), $objectIds);
		}
		
		return $this;
	}

	/**
	 * Format a string to a valid URL key
	 * Allow a-zA-Z0-9, hyphen and /
	 *
	 * @param string $str
	 * @return string
	 */
	public function formatUrlKey($str)
	{
		$urlKey = str_replace("'", '', $str);
		$urlKey = preg_replace('#[^0-9a-z\/]+#i', '-', Mage::helper('catalog/product_url')->format($urlKey));
		$urlKey = strtolower($urlKey);
		$urlKey = trim($urlKey, '-');
		
		return $urlKey;
	}
}
