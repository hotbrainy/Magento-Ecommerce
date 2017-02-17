<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */ 
class Amasty_Shopby_Model_Mysql4_Decimal extends Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Decimal 
{
    protected $_minMax = null;

    /**
     * @param Amasty_Shopby_Model_Catalog_Layer_Filter_Decimal $filter
     * @param float $from
     * @param float $to
     * @return Mage_Catalog_Model_Resource_Layer_Filter_Decimal
     */
    public function applyFilterToCollection($filter, $from, $to)
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        if ($helper->useSolr())
        {
            $this->_applyFilterToCollectionSolr($filter, $from, $to);
        } else {
            $this->_applyFilterToCollectionDb($filter, $from, $to);
        }
    }

    /**
     * @param Amasty_Shopby_Model_Catalog_Layer_Filter_Decimal $filter
     * @param float $from
     * @param float $to
     */
    protected function _applyFilterToCollectionSolr($filter, $from, $to)
    {
        /** @var Enterprise_Search_Model_Resource_Collection $collection */
        $collection = $filter->getLayer()->getProductCollection();
        $attributeCode  = $filter->getAttributeModel()->getAttributeCode();

        $field = 'attr_decimal_'. $attributeCode;
        $value = array(
            $field => array(
                'from' => $from,
                'to'   => $to,
            )
        );

        $collection->addFqFilter($value);
    }

    /**
     * @param Amasty_Shopby_Model_Catalog_Layer_Filter_Decimal $filter
     * @param float $from
     * @param float $to
     */
    protected function _applyFilterToCollectionDb($filter, $from, $to)
    {
        $collection = $filter->getLayer()->getProductCollection();
        $attribute  = $filter->getAttributeModel();

        $connection = $this->_getReadAdapter();

        $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());

        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
        );


        $collection->getSelect()->join(
            array($tableAlias => $this->getMainTable()),
            implode(' AND ', $conditions),
            array()
        );

        // bundle items has 2 records if single item has special price
        if (Mage::getStoreConfig('amshopby/general/bundle')){
            $collection->getSelect()->distinct(true);
        }

        list($min, $max) = $this->getMinMax($filter);

		$settings = $filter->getSettings();
		$isSlider = (isset($settings['display_type']) && $settings['display_type'] == Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_SLIDER);

        $toSign = ($max == $to || $isSlider || $from == $to) ? '<=' : '<';
        $collection->getSelect()->where("{$tableAlias}.value >= ?", $from);

        if ($to) {
            $collection->getSelect()->where("{$tableAlias}.value {$toSign} ?", $to);
        }
    }

    /**
     * @param Amasty_Shopby_Model_Catalog_Layer_Filter_Decimal $filter
     * @return array (max, min)
     */
    public function getMinMax($filter)
    {
        if (is_null($this->_minMax)) {
            /** @var Amasty_Shopby_Helper_Data $helper */
            $helper = Mage::helper('amshopby');
            if ($helper->useSolr())
            {
                $this->_computeMinMaxSolr($filter);
            } else {
                $this->_minMax = parent::getMinMax($filter);
            }
        }
        return $this->_minMax;
    }

    /**
     * @param Amasty_Shopby_Model_Catalog_Layer_Filter_Decimal $filter
     */
    protected function _computeMinMaxSolr($filter)
    {
        /** @var Enterprise_Search_Model_Resource_Engine $engine */
        $engine = Mage::getModel('enterprise_search/resource_engine');

        /** @var Enterprise_Search_Model_Resource_Collection $productCollection */
        $productCollection = $filter->getLayer()->getProductCollection();
        $filters = $productCollection->getExtendedSearchParams();

        $queryText = $filters['query_text'];
        $query = $queryText ? $queryText : array('*' => '*');
        unset($filters['query_text']);

        $attribute_code = $filter->getAttributeModel()->getAttributeCode();
        $paramName = 'attr_decimal_' . $attribute_code;
        unset($filters[$paramName]);

        $store  = Mage::app()->getStore();
        $params = array(
            'store_id'          => $store->getId(),
            'locale_code'       => $store->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE),
            'filters'           => $filters,
            'limit'             => 0,
            'ignore_handler'    => empty($queryText),
            'solr_params'       => array(
                'stats'             => 'true',
                'stats.field'       => array($paramName),
            ),
        );
        $stats = $engine->getStats($query, $params);

        $this->_minMax = array($stats[$paramName]['min'], $stats[$paramName]['max']);
    }

    /**
     * Retrieve clean select with joined index table
     * Joined table has index
     *
     * @param Amasty_Shopby_Model_Catalog_Layer_Filter_Decimal $filter
     * @return Varien_Db_Select
     */
    protected function _getSelect($filter)
    {
        /** @var Enterprise_Search_Model_Resource_Collection $collection */
        $collection = $filter->getLayer()->getProductCollection();

        // clone select from collection with filters
        $select = clone $collection->getSelect();
        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        
        $attributeId = $filter->getAttributeModel()->getId();
        $storeId     = $collection->getStoreId();

        $select->join(
            array('decimal_index' => $this->getMainTable()),
            'e.entity_id = decimal_index.entity_id'.
            ' AND ' . $this->_getReadAdapter()->quoteInto('decimal_index.attribute_id = ?', $attributeId) .
            ' AND ' . $this->_getReadAdapter()->quoteInto('decimal_index.store_id = ?', $storeId),
            array()
        );
        
        $code = $filter->getAttributeModel()->getAttributeCode();
        
        $field = $code . "_idx.value";
        
        /*
         * Reset where condition of current filter
         */
        $oldWhere = $select->getPart(Varien_Db_Select::WHERE);
        $newWhere = array();
        
        foreach ($oldWhere as $cond) {
            if (false === strpos($cond, $field)) {
                $newWhere[] = $cond;
            }
        }
        if ($newWhere && substr($newWhere[0], 0, 3) == 'AND') {
            $newWhere[0] = substr($newWhere[0], 3); 
        }
                      
        $select->setPart(Varien_Db_Select::WHERE, $newWhere); 
        return $select;
    }
}