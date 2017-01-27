<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_Extradownloads
 * @copyright  Copyright (c) 2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

/**
 * Extra Downloads File Model
 */
class AW_Extradownloads_Model_Entity_File extends Mage_Eav_Model_Entity_Abstract
{
    /**
     * Identifuer of default store
     * used for loading default data for entity
     */
    const DEFAULT_STORE_ID = 0;

    /**
     * Initiate resources
     */
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('extradownloads_file');
        $this->setConnection(
            $resource->getConnection('extradownloads_read'),
            $resource->getConnection('extradownloads_write')
        );
    }

    /**
     * Retrieve file entity default attributes
     * @return array
     */
    protected function _getDefaultAttributes()
    {
        return array(
            'entity_type_id',
            'attribute_set_id',
            'created_at',
            'updated_at',
            'increment_id',
            'product_id',
        );
    }

    /**
     * Retrives default store id
     * @return int
     */
    public function getDefaultStoreId()
    {
        return self::DEFAULT_STORE_ID;
    }

    /**
     * Check for store value update needs
     *
     * @param array $newData Array with new data
     * @param string $key Attribute key-code for check
     * @return boolean
     */
    protected function _checkForStoreUpdateFlag($newData, $key){
        foreach ($newData as $k=>$v){
            if ($k == 'need_store_update_'.$key){
                return true;
            }
        }
        return false;
    }


    /**
     * Prepare entity object data for save
     *
     * result array structure:
     * array (
     *  'newObject', 'entityRow', 'insert', 'update', 'delete'
     * )
     *
     * @param   Varien_Object $newObject
     * @return  array
     */
    protected function _collectSaveData($newObject)
    {
        $newData = $newObject->getData();

        $entityId = $newObject->getData($this->getEntityIdField());
        if (!empty($entityId)) {
            /**
             * get current data in db for this entity
             */
            /*$className  = get_class($newObject);
            $origObject = new $className();
            $origObject->setData(array());
            $this->load($origObject, $entityId);
            $origData = $origObject->getOrigData();*/
            $origData = $this->_getOrigObject($newObject)->getOrigData();

            /**
             * drop attributes that are unknown in new data
             * not needed after introduction of partial entity loading
             */
            foreach ($origData as $k=>$v) {
                if (!array_key_exists($k, $newData)) {
                    unset($origData[$k]);
                }
            }
        }

        foreach ($newData as $k=>$v) {
            /**
             * Check attribute information
             */
            if (is_numeric($k) || is_array($v)) {
                continue;
                throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid data object key'));
            }

            $attribute = $this->getAttribute($k);
            if (empty($attribute)) {
                continue;
            }

            $attrId = $attribute->getAttributeId();

            /**
             * if attribute is static add to entity row and continue
             */
            if ($this->isAttributeStatic($k)) {
                $entityRow[$k] = $this->_prepareStaticValue($k, $v);
                continue;
            }

            $valueId = $attribute->getBackend()->getValueId();
            if (method_exists($attribute->getBackend(), 'getEntityValueId')) {
                $valueId = $attribute->getBackend()->getEntityValueId($newObject);
            }

            /**
             * Check comparability for attribute value
             */
            if (isset($origData[$k])) {
                if ($attribute->isValueEmpty($v)) {
                    $delete[$attribute->getBackend()->getTable()][] = array(
                        'attribute_id'  => $attrId,
                        'value_id'      => $valueId
                    );
                }
                /**
                 * Modification for per-store value saving
                 */
                elseif ($v!==$origData[$k] || $this->_checkForStoreUpdateFlag($newData, $k)) {
                    $update[$attrId] = array(
                        'value_id' => $valueId,
                        'value'    => $v,
                    );
                }
            }
            elseif (!$attribute->isValueEmpty($v)) {
                $insert[$attrId] = $v;
            }
        }

        $result = compact('newObject', 'entityRow', 'insert', 'update', 'delete');
        return $result;
    }

    /**
     * Retrieve select object for loading entity attributes values
     *
     * Join attribute store value
     *
     * @param   Varien_Object $object
     * @param   mixed $rowId
     * @return  Zend_Db_Select
     */
    protected function _getLoadAttributesSelect($object, $table)
    {
        /**
         * This condition is applicable for all cases when we was work in not single
         * store mode, customize some value per specific store view and than back
         * to single store mode. We should load correct values
         */
        if (Mage::app()->isSingleStoreMode()) {
            $storeId = Mage::app()->getStore(true)->getId();
        } else {
            $storeId = $object->getStoreId();
        }

        $select = $this->_read->select()
            ->from(array('default' => $table));
        if ($setId = $object->getAttributeSetId()) {
            $select->join(
                array('set_table' => $this->getTable('eav/entity_attribute')),
                'default.attribute_id=set_table.attribute_id AND '
                    . 'set_table.attribute_set_id=' . intval($setId),
                array()
            );
        }

        $joinCondition = 'main.attribute_id=default.attribute_id AND '
            . $this->_read->quoteInto('main.store_id=? AND ', intval($storeId))
            . $this->_read->quoteInto('main.'.$this->getEntityIdField() . '=?', $object->getId());

        $select->joinLeft(
            array('main' => $table),
            $joinCondition,
            array(
                'store_value_id' => 'value_id',
                'store_value'    => 'value'
            ))
            ->where('default.'.$this->getEntityIdField() . '=?', $object->getId())
            ->where('default.store_id=?', $this->getDefaultStoreId());

        return $select;
    }

    /**
     * Initialize attribute value for object
     *
     * @param   Varien_Object $object
     * @param   array $valueRow
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _setAttribteValue($object, $valueRow)
    {
        parent::_setAttribteValue($object, $valueRow);
        if ($attribute = $this->getAttribute($valueRow['attribute_id'])) {
            $attributeCode = $attribute->getAttributeCode();
            if (isset($valueRow['store_value'])) {
                $object->setAttributeDefaultValue($attributeCode, $valueRow['value']);
                $object->setData($attributeCode, $valueRow['store_value']);
                $attribute->getBackend()->setValueId($valueRow['store_value_id']);
            }
        }
        return $this;
    }

    /**
     * Insert entity attribute value
     *
     * Insert attribute value we do only for default store
     *
     * @param   Varien_Object $object
     * @param   Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param   mixed $value
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _insertAttribute($object, $attribute, $value)
    {
        $entityIdField = $attribute->getBackend()->getEntityIdField();
        $row = array(
            $entityIdField  => $object->getId(),
            'entity_type_id'=> $object->getEntityTypeId(),
            'attribute_id'  => $attribute->getId(),
            'value'         => $this->_prepareValueForSave($value, $attribute),
            'store_id'      => $this->getDefaultStoreId()
        );
        $fields = array();
        $values = array();
        foreach ($row as $k => $v) {
            $fields[] = $this->_getWriteAdapter()->quoteIdentifier('?', $k);
            $values[] = $this->_getWriteAdapter()->quoteInto('?', $v);
        }
        $sql = sprintf('INSERT IGNORE INTO %s (%s) VALUES(%s)',
            $this->_getWriteAdapter()->quoteIdentifier($attribute->getBackend()->getTable()),
            join(',', array_keys($row)),
            join(',', $values));
        $this->_getWriteAdapter()->query($sql);
        if (!$lastId = $this->_getWriteAdapter()->lastInsertId()) {
            $select = $this->_getReadAdapter()->select()
                ->from($attribute->getBackend()->getTable(), 'value_id')
                ->where($entityIdField . '=?', $row[$entityIdField])
                ->where('entity_type_id=?', $row['entity_type_id'])
                ->where('attribute_id=?', $row['attribute_id'])
                ->where('store_id=?', $row['store_id']);
            $lastId = $select->query()->fetchColumn();
        }

        /**
         *  If onsert attribute for store, then use this data for
         *  default Attribure Value and Save it for store data
         */
        if ($object->getStoreId() != $this->getDefaultStoreId()) {
            try {
                $this->_updateAttribute($object, $attribute, $lastId, $value); 
            } catch(Exception $e) {
                Mage::throwException($e->getMessage());
            }            
        }
        return $this;
    }

    /**
     * Update entity attribute value
     *
     * @param   Varien_Object $object
     * @param   Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param   mixed $valueId
     * @param   mixed $value
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _updateAttribute($object, $attribute, $valueId, $value)
    {
        /**
         * If we work in single store mode all values should be saved just
         * for default store id
         * In this case we clear all not default values
         */
        if (Mage::app()->isSingleStoreMode()) {
            $this->_getWriteAdapter()->delete(
                $attribute->getBackend()->getTable(),
                $this->_getWriteAdapter()->quoteInto('attribute_id=?', $attribute->getId()) .
                $this->_getWriteAdapter()->quoteInto(' AND entity_id=?', $object->getId()) .
                $this->_getWriteAdapter()->quoteInto(' AND store_id!=?', Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
            );
        }

        /**
         * Update attribute value for store
         */
        if ($object->getStoreId()) {
            $this->_updateAttributeForStore($object, $attribute, $value, $object->getStoreId());
        }
        /**
         * Update value for default store
         */
        else {
            $this->_getWriteAdapter()->update($attribute->getBackend()->getTable(),
                array('value' => $this->_prepareValueForSave($value, $attribute)),
                'value_id='.(int)$valueId
            );
        }
        return $this;
    }

    /**
     * Update attribute value for specific store
     *
     * @param   Mage_Catalog_Model_Abstract $object
     * @param   object $attribute
     * @param   mixed $value
     * @param   int $storeId
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Abstract
     */
    protected function _updateAttributeForStore($object, $attribute, $value, $storeId)
    {
        $entityIdField = $attribute->getBackend()->getEntityIdField();
        $select = $this->_getWriteAdapter()->select()
            ->from($attribute->getBackend()->getTable(), 'value_id')
            ->where('entity_type_id=?', $object->getEntityTypeId())
            ->where("$entityIdField=?",$object->getExtradownloadsId())
            ->where('store_id=?', $storeId)
            ->where('attribute_id=?', $attribute->getId());        

        /**
         * When value for store exist
         */
        if ($valueId = $this->_getWriteAdapter()->fetchOne($select)) {
            $this->_getWriteAdapter()->update($attribute->getBackend()->getTable(),
                array('value' => $this->_prepareValueForSave($value, $attribute)),
                'value_id='.$valueId
            );
        } else {        
            $row = array(
                $entityIdField  => $object->getExtradownloadsId() ? $object->getExtradownloadsId() : $object->getId(),
                'entity_type_id'=> $object->getEntityTypeId(),
                'attribute_id'  => $attribute->getId(),
                'value'         => $this->_prepareValueForSave($value, $attribute),
                'store_id'      => $storeId
            );

            $fields = array();
            $values = array();
            foreach ($row as $k => $v) {
                $fields[] = $this->_getWriteAdapter()->quoteIdentifier('?', $k);
                $values[] = $this->_getWriteAdapter()->quoteInto('?', $v);
            }
            $sql = sprintf('INSERT IGNORE INTO %s (%s) VALUES(%s)',
                $this->_getWriteAdapter()->quoteIdentifier($attribute->getBackend()->getTable()),
                join(',', array_keys($row)),
                join(',', $values));
            $this->_getWriteAdapter()->query($sql);
        }
        return $this;
    }

    /**
     * Delete entity attribute values
     *
     * @param   Varien_Object $object
     * @param   string $table
     * @param   array $info
     * @return  Varien_Object
     */
    protected function _deleteAttributes($object, $table, $info)
    {
        $entityIdField      = $this->getEntityIdField();
        $globalValues       = array();
        $websiteAttributes  = array();
        $storeAttributes    = array();

        /**
         * Separate attributes by scope
         */
        foreach ($info as $itemData) {
            $attribute = $this->getAttribute($itemData['attribute_id']);
//            if ($attribute->isScopeStore()) {
//                $storeAttributes[] = $itemData['attribute_id'];
//            }
//            elseif ($attribute->isScopeWebsite()) {
//                $websiteAttributes[] = $itemData['attribute_id'];
//            }
//            else {
                $globalValues[] = $itemData['value_id'];
//            }
        }

        /**
         * Delete global scope attributes
         */
        if (!empty($globalValues)) {
            $condition = $this->_getWriteAdapter()->quoteInto('value_id IN (?)', $globalValues);
            $this->_getWriteAdapter()->delete($table, $condition);
        }

        $condition = $this->_getWriteAdapter()->quoteInto("$entityIdField=?", $object->getId())
            . $this->_getWriteAdapter()->quoteInto(' AND entity_type_id=?', $object->getEntityTypeId());
        
        /**
         * Delete website scope attributes
         */
        if (!empty($websiteAttributes)) {
            $storeIds = $object->getWebsiteStoreIds();
            if (!empty($storeIds)) {
                $delCondition = $condition
                    . $this->_getWriteAdapter()->quoteInto(' AND attribute_id IN(?)', $websiteAttributes)
                    . $this->_getWriteAdapter()->quoteInto(' AND store_id IN(?)', $storeIds);
                $this->_getWriteAdapter()->delete($table, $delCondition);
            }
        }

        /**
         * Delete store scope attributes
         */
        if (!empty($storeAttributes)) {
            $delCondition = $condition
                . $this->_getWriteAdapter()->quoteInto(' AND attribute_id IN(?)', $storeAttributes)
                . $this->_getWriteAdapter()->quoteInto(' AND store_id =?', $object->getStoreId());
            $this->_getWriteAdapter()->delete($table, $delCondition);;
        }
        return $this;
    }

    /**
     * Retrives flag use default title value
     * @param Integer $entity_id File entity id
     * @return Boolean
     */
    public function getUseDefaultTitle($entity_id)
    {
        return $this->getUseDefaultValueFlag('title', $entity_id);
    }

    /**
     * Retrives flag use default visible value
     * @param Integer $entity_id File entity id
     * @return Boolean
     */
    public function getUseDefaultVisible($entity_id)
    {
        return $this->getUseDefaultValueFlag('visible', $entity_id);
    }

    /**
     * Retrives flag use default file values
     * @param Integer $entity_id File entity id
     * @return Boolean
     */
    public function getUseDefaultType($entity_id)
    {
        return $this->getUseDefaultValueFlag('type', $entity_id);
    }

    /**
     * Retrives flag use default sort order value
     * @param Integer $entity_id File entity id
     * @return Boolean
     */
    public function getUseDefaultSortOrder($entity_id)
    {
        return $this->getUseDefaultValueFlag('sort_order', $entity_id);
    }

    /**
     * Check attribute for default value used
     * @param String $attribute Attribute to check
     * @param Integer $entity_id File entity id
     * @return Boolean
     */
    public function getUseDefaultValueFlag($attribute, $entity_id)
    {        
        $attribute = $this->getAttribute($attribute);
        $attribute_id = $attribute->getAttributeId();
        $entity_type_id = $attribute->getEntityTypeId();
        $table = $attribute->getBackendTable();
        $store_id = Mage::registry('current_product')->getStoreId();
        $connection = $this->_getReadAdapter();

        $select = new Zend_Db_Select($connection);
        $select
            ->from($table, array('count'=>'COUNT(*)'))
            ->where('entity_type_id = ?', $entity_type_id)
            ->where('attribute_id = ?', $attribute_id)
            ->where('entity_id = ?', $entity_id)
            ->where('store_id = ?', $store_id)
            ;
        try {
            $res = $select->getAdapter()->fetchOne($select->__toString());
        } catch(Exception $e) {
            $res = 0;
            Mage::throwException($e->getMessage());
        }
        return ($res == 0);
    }

    /**
     * Reset value to default if flag is set
     *
     * @param   Mage_Catalog_Model_Abstract $object
     * @param   String $attribute_code
     * @param   int $storeId
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Abstract
     */
    public function resetToDefault($object, $attribute_code, $storeId)
    {         
        if ($attribute_code == 'type'){
            $this->resetToDefault($object, 'file', $storeId);
            $this->resetToDefault($object, 'url', $storeId);
        }

        $attribute = $this->getAttribute($attribute_code);

        $this->_getWriteAdapter()->delete(
            $attribute->getBackend()->getTable(),
            $this->_getWriteAdapter()->quoteInto('attribute_id=?', $attribute->getId()) .
            $this->_getWriteAdapter()->quoteInto(' AND entity_id=?', $object->getExtradownloadsId()) .
            $this->_getWriteAdapter()->quoteInto(' AND store_id=?', $storeId)
        );
        
        return $this;
    }
}