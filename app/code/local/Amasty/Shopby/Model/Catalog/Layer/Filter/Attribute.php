<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * Class Amasty_Shopby_Model_Catalog_Layer_Filter_Attribute
 * @method Mage_Catalog_Model_Resource_Eav_Attribute getAttributeModel()
 */
class Amasty_Shopby_Model_Catalog_Layer_Filter_Attribute extends Amasty_Shopby_Model_Catalog_Layer_Filter_Attribute_Adapter
{
    /** @var  Amasty_Shopby_Model_Filter */
    protected $filterSettings = null;

    /**
     * @deprecated
     */
    protected function _getCount($attribute)
    {
        // clone select from collection with filters
        $select = $this->_getBaseCollectionSql();

        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::GROUP);

        $connection = $this->_getResource()->getReadConnection();
        $tableAlias = $attribute->getAttributeCode() . '_idx';
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $this->getStoreId()),
        );

        $select
            ->join(
                array($tableAlias => $this->_getResource()->getMainTable()),
                join(' AND ', $conditions),
                array('value', 'count' => "COUNT(DISTINCT {$tableAlias}.entity_id)"))
            ->group("{$tableAlias}.value");

        $optionsCount = $connection->fetchPairs($select);

        return $optionsCount;       
    }
    
    protected function _getAttributeTableAlias()
    {
        $alias = $this->getAttributeModel()->getAttributeCode() . '_idx';
        return $alias;
    }

    public function applyFilterToCollection($value, $notUsingFieldForCompatibilityWithEnterprise = null) {
        $attribute  = $this->getAttributeModel();
        $collection = $this->getLayer()->getProductCollection();
        if (Mage::helper('amshopby')->useSolr()) {
            $fieldName = Mage::getResourceSingleton('enterprise_search/engine')
                ->getSearchEngineFieldName($attribute, 'nav');
            $prefix = '{!tag=' . $attribute->getAttributeCode() . '}';
            $collection->addFqFilter(array($prefix . $fieldName => $value));
        } else {
            $alias      = $this->_getAttributeTableAlias();
            $connection = $this->_getResource()->getReadConnection();

            if ($this->getUseAndLogic()) {
                foreach ($value as $i => $attrValue) {
                    $alias = $alias . $i;
                    $conditions = array(
                        "{$alias}.entity_id = e.entity_id",
                        $connection->quoteInto("{$alias}.attribute_id = ?", $attribute->getAttributeId()),
                        $connection->quoteInto("{$alias}.store_id = ?",     $collection->getStoreId()),
                        $connection->quoteInto("{$alias}.value = ?",      $attrValue)
                    );

                    $collection->getSelect()->join(
                        array($alias => $this->_getResource()->getMainTable()),
                        join(' AND ', $conditions),
                        array()
                    );
                }
            } else {

                $conditions = array(
                    "{$alias}.entity_id = e.entity_id",
                    $connection->quoteInto("{$alias}.attribute_id = ?", $attribute->getAttributeId()),
                    $connection->quoteInto("{$alias}.store_id = ?",     $collection->getStoreId()),
                    $connection->quoteInto("{$alias}.value IN(?)",      $value)
                );

                $collection->getSelect()->join(
                    array($alias => $this->_getResource()->getMainTable()),
                    join(' AND ', $conditions),
                    array()
                );
            }
        }

        if (isset($_REQUEST['debug'])) {
            Zend_Debug::dump($collection->getSelect()->__toString());
        }
        
        if (count($value) > 1){
            $collection->getSelect()->distinct(true);
        }


    }

    /**
     * Apply attribute option filter to product collection
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @param   Varien_Object $filterBlock
     * @return  Mage_Catalog_Model_Layer_Filter_Attribute
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $currentVals = Mage::helper('amshopby')->getRequestValues($this->_requestVar);
        if ($currentVals) {

            $attributeCode = $this->getAttributeModel()->getAttributeCode();
            /** @var Amasty_Shopby_Helper_Attributes $attributeHelper */
            $attributeHelper = Mage::helper('amshopby/attributes');
            if (!$attributeHelper->lockApplyFilter($attributeCode, 'attr')) {
                return $this;
            }

            $this->applyFilterToCollection($currentVals);

            // check if need to add state
            $controller = Mage::app()->getRequest()->getControllerModule();
            $branding = $controller == 'Amasty_Shopby'
                && count($currentVals) == 1
                && trim(Mage::getStoreConfig('amshopby/brands/attr')) == $attributeCode;
            if (!$branding) {
                $this->addState($currentVals);
            }

            if (count($currentVals) > 1) {
                /** @var Amasty_Shopby_Helper_Layer_Cache $cache */
                $cache = Mage::helper('amshopby/layer_cache');
                $cache->limitLifetime(Amasty_Shopby_Helper_Layer_Cache::LIFETIME_SESSION);
            }
        }
        return $this;
    }

    protected function addState($currentVals)
    {
        //generate Status Block
        $attribute = $this->getAttributeModel();
        $text = '';
        $options = Mage::helper('amshopby/attributes')->getAttributeOptions($attribute->getAttributeCode());

        $children = array();

        foreach ($options as $option) {
            $k = array_search($option['value'], $currentVals);
            if (false !== $k){

                $exclude = $currentVals;
                unset($exclude[$k]);
                $exclude = implode(',', $exclude);
                if (!$exclude)
                    $exclude = null;

                $query = array(
                    $this->getRequestVar() => $exclude,
                    Mage::getBlockSingleton('page/html_pager')->getPageVarName() => null // exclude current page from urls
                );
                $url = Mage::helper('amshopby/url')->getFullUrl($query);

                $text .= $option['label'] . " ";

                $children[] = array(
                    'label' => $option['label'],
                    'url' => $url,
                );
            }
        }

        /** @var Amasty_Shopby_Model_Catalog_Layer_Filter_Item $state */
        $state = $this->_createItem($text, $currentVals)
            ->setVar($this->_requestVar);

        if (count($children) > 1) {
            $state->setData('children', $children);
        }

        $this->getLayer()->getState()->addFilter($state);
    }

    public function addFacetCondition()
    {
        $code = $this->getAttributeModel()->getAttributeCode();
        if (!$code) {
            return;
        }

        $key = 'amshopby_facet_added_' . $code;
        if (Mage::registry($key)) {
            return;
        }

        $engine = Mage::getResourceSingleton('enterprise_search/engine');
        $facetField = $engine->getSearchEngineFieldName($this->getAttributeModel(), 'nav');
        $prefix = '{!ex=' . $this->getAttributeModel()->getAttributeCode() . '}';
        $this->getLayer()->getProductCollection()->setFacetCondition($prefix . $facetField);

        Mage::register($key, true);
    }

    /**
     * Get data array for building attribute filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');

        /** @var Amasty_Shopby_Helper_Layer_Cache $cache */
        $cache = Mage::helper('amshopby/layer_cache');
        $cache->setStateKey($this->getLayer()->getStateKey());
        $key = 'A_' . $this->_requestVar;
        $data = $cache->getFilterItems($key);

        if (is_null($data)) {
            if ($helper->useSolr()) {
                $engine = Mage::getResourceSingleton('enterprise_search/engine');
                $fieldName = $engine->getSearchEngineFieldName($attribute, 'nav');
                $productCollection = $this->getLayer()->getProductCollection();
                $optionsCount = $productCollection->getFacetedData($fieldName);

                $options = $attribute->getSource()->getAllOptions(false);
            } else {
                $options = Mage::helper('amshopby/attributes')->getAttributeOptions($attribute->getAttributeCode());
                $optionsCount = $this->_getCount($attribute);
            }

            $data = array();
            $currentVals = $helper->getRequestValues($this->_requestVar);

            foreach ($options as $option) {
                if (is_array($option['value'])) {
                    continue;
                }
                if (!Mage::helper('core/string')->strlen($option['value'])) {
                    continue;
                }

                $ind = array_search($option['value'], $currentVals);

                $itemValue = $currentVals;
                if (false === $ind){
                    if ($this->getSingleChoice()) {
                        $itemValue = array($option['value']);
                    } else {
                        $itemValue[] = $option['value'];
                    }
                }
                else {
                    $itemValue[$ind]  = null;
                    unset($itemValue[$ind]);
                }

                $itemValue = implode(',', $itemValue);
                $cnt = isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0;
                if ($cnt || $this->_getIsFilterableAttribute($attribute) != self::OPTIONS_ONLY_WITH_RESULTS) {
                    $data[] = array(
                        'label'     => $option['label'],
                        'value'     => $itemValue,
                        'count'     => $cnt,
                        'option_id' => $option['value'],
                        'is_featured'  => (int) Mage::helper('amshopby/attributes')->getIsOptionFeatured($option['value']),
                    );
                }
                
               
            }

            $cache->setFilterItems($key, $data);
        }

        return $data;
    }

    protected function _initItems()
    {
        $data  = $this->_getItemsData();
        $items = array();
        foreach ($data as $itemData) {
            $item = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count']
            );
            $item->setOptionId($itemData['option_id']);
            $item->setIsFeatured($itemData['is_featured']);
            $items[] = clone $item;
        }
        $this->_items = $items;
        return $this;
    } 
    
    //start new functions
    
    // will work for both 1.3 and 1.4
    protected function _getBaseCollectionSql()
    {
        $alias = $this->_getAttributeTableAlias();
        
        $baseSelect = clone parent::_getBaseCollectionSql();
        
        $oldWhere = $baseSelect->getPart(Varien_Db_Select::WHERE);
        $newWhere = array();

        foreach ($oldWhere as $cond){
            if (!strpos($cond, $alias)){
                $newWhere[] = $cond;
            }
        }
  
        if ($newWhere && substr($newWhere[0], 0, 3) == 'AND'){
            $newWhere[0] = substr($newWhere[0], 3);
        }
        
        $baseSelect->setPart(Varien_Db_Select::WHERE, $newWhere);
        
        $oldFrom = $baseSelect->getPart(Varien_Db_Select::FROM);
        $newFrom = array();
        
        foreach ($oldFrom as $name=>$val){
            if ($name != $alias){
                $newFrom[$name] = $val;
            }
        }
        $baseSelect->setPart(Varien_Db_Select::FROM, $newFrom);

        return $baseSelect;
    }

    protected function getUseAndLogic()
    {
        $settings = $this->getFilterSettings();
        return $settings ? $settings->getUseAndLogic() : null;
    }

    protected function getSingleChoice()
    {
        $settings = $this->getFilterSettings();
        if (!$settings) {
            return null;
        }
        return ($settings->getDisplayType() == Amasty_Shopby_Model_Source_Attribute::DT_DROPDOWN) ? true : $settings->getSingleChoice();
    }

    protected function getFilterSettings()
    {
        if (is_null($this->filterSettings)) {
            /** @var Amasty_Shopby_Helper_Data $helper */
            $helper = Mage::helper('amshopby');
            $attribute  = $this->getAttributeModel();
            $settings = $helper->getAttributesSettings();
            if (isset($settings[$attribute->getAttributeId()])) {
                $this->filterSettings = $settings[$attribute->getAttributeId()];
            } else {
                $this->filterSettings = null;
            }
        }

        return $this->filterSettings;
    }
}