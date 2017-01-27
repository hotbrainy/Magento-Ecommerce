<?php

/**
 * Extra Downloads File Collection
 */
class AW_Extradownloads_Model_Entity_File_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Identifuer of default store
     * used for loading default data for entity
     */
    const DEFAULT_STORE_ID = 0;

    /**
     * Store Id
     * @var integer
     */
    protected $_storeId = null;

    /**
     * Class constructor
     */
    protected function _construct()
    {
        $this->_init('extradownloads/file');
    }
    
    /**
     * Set up store to collection
     * @param Integer|String|Mage_Core_Model_Store $store
     * @return AW_Extradownloads_Model_Entity_File_Collection
     */
    public function setStore($store)
    {
        $this->setStoreId(Mage::app()->getStore($store)->getId());
        return $this;
    }

    /**
     * Set up store by store id to collection
     * @param Integer|String $storeId
     * @return AW_Extradownloads_Model_Entity_File_Collection
     */
    public function setStoreId($storeId)
    {
        if ($storeId instanceof Mage_Core_Model_Store) {
            $storeId = $storeId->getId();
        }
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Retrives store id that setted for collection
     * If it's missed, the current store will be setted up
     * @return Integer
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->setStoreId(Mage::app()->getStore()->getId());
        }
        return $this->_storeId;
    }

    /**
     * Retrives store id of default store (it's zero [0])
     * @return Integer
     */
    public function getDefaultStoreId()
    {
        return self::DEFAULT_STORE_ID;
    }

    /**
     * Retrieve attributes load select
     *
     * @param   string $table
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getLoadAttributesSelect($table, $attributeIds = array())
    {
        if (empty($attributeIds)) {
            $attributeIds = $this->_selectAttributes;
        }
        if ((int) $this->getStoreId()) {
            $entityIdField = $this->getEntity()->getEntityIdField();
            $joinCondition = 'store.attribute_id=' . $table . '.attribute_id
                AND store.entity_id=' . $table . '.entity_id
                AND store.store_id='.(int) $this->getStoreId();

            $select = $this->getConnection()->select()
                ->from(array($table), array($entityIdField, 'attribute_id', 'default_value'=>'value'))
                ->joinLeft(
                    array('store'=>$table),
                    $joinCondition,
                    array(
                        'value' => new Zend_Db_Expr("IFNULL(store.value, {$table}.value)")
                    )
                )
                ->where($table . '.entity_type_id=?', $this->getEntity()->getTypeId())
                ->where($table . '.' . $entityIdField . ' in (?)', array_keys($this->_itemsById))
                ->where($table . '.attribute_id in (?)', $attributeIds)
                ->where($table . '.store_id = 0');
        }
        else {
            $select = parent::_getLoadAttributesSelect($table)
                ->where($table . '.store_id=?', $this->getDefaultStoreId());
        }
        return $select;
    }

    /**
     * Adding join statement to collection select instance
     * 
     * @param   string $method
     * @param   object $attribute
     * @param   string $tableAlias
     * @param   array $condition
     * @param   string $fieldCode
     * @param   string $fieldAlias
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias)
    {
        if (isset($this->_joinAttributes[$fieldCode]['store_id'])) {
            $store_id = $this->_joinAttributes[$fieldCode]['store_id'];
        }
        else {
            $store_id = $this->getStoreId();
        }
        if ($store_id != $this->getDefaultStoreId() && !$attribute->getIsGlobal()) {
            /**
             * Add joining default value for not default store
             * if value for store is null - we use default value
             */
            $defCondition = '('.join(') AND (', $condition).')';
            $defAlias     = $tableAlias.'_default';
            $defFieldCode = $fieldCode.'_default';
            $defFieldAlias= str_replace($tableAlias, $defAlias, $fieldAlias);

            $defCondition = str_replace($tableAlias, $defAlias, $defCondition);
            $defCondition.= $this->getConnection()->quoteInto(" AND $defAlias.store_id=?", $this->getDefaultStoreId());

            $this->getSelect()->$method(
                array($defAlias => $attribute->getBackend()->getTable()),
                $defCondition,
                array()
            );

            $method = 'joinLeft';
            $fieldAlias = new Zend_Db_Expr("IFNULL($fieldAlias, $defFieldAlias)");
            $this->_joinAttributes[$fieldCode]['condition_alias'] = $fieldAlias;
            $this->_joinAttributes[$fieldCode]['attribute']       = $attribute;
        }
        else {
            $store_id = $this->getDefaultStoreId();
        }
        $condition[] = $this->getConnection()->quoteInto("$tableAlias.store_id=?", $store_id);
        return parent::_joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias);
    }
}