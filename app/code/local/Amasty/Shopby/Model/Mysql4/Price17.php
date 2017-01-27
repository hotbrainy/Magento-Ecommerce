<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


class Amasty_Shopby_Model_Mysql4_Price17 extends Mage_Catalog_Model_Resource_Layer_Filter_Price
{

    public $_maxMinPrice = null;

    /** @var Amasty_Shopby_Helper_Data */
    protected $_dataHelper;

    protected function _construct()
    {
        parent::_construct();
        $this->_dataHelper = Mage::helper('amshopby');
    }
    
    /**
     * Retrieve clean select with joined price index table
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @return Varien_Db_Select
     */
    protected function _getSelect($filter)
    {
        if (!$this->_dataHelper->isOnLandingPage()) {
            return parent::_getSelect($filter);
        }
        
        $collection = $filter->getLayer()->getProductCollection();
        $collection->addPriceData($filter->getCustomerGroupId(), $filter->getWebsiteId());

        if (!is_null($collection->getCatalogPreparedSelect())) {
            $select = clone $collection->getCatalogPreparedSelect();
        } else {
            $select = clone $collection->getSelect();
        }

        // reset columns, order and limitation conditions
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);

        // remove join with main table
        $fromPart = $select->getPart(Zend_Db_Select::FROM);
        if (!isset($fromPart[Mage_Catalog_Model_Resource_Product_Collection::INDEX_TABLE_ALIAS])
            || !isset($fromPart[Mage_Catalog_Model_Resource_Product_Collection::MAIN_TABLE_ALIAS])
        ) {
            return $select;
        }

        $select->where($this->_getPriceExpression($filter, $select) . ' IS NOT NULL');
        
        return $select;
            
    }
    
    protected function _getPriceExpression($filter, $select, $replaceAlias = true)
    {
        if ($this->_dataHelper->isOnLandingPage()) {
            $replaceAlias = false;
        }
        return parent::_getPriceExpression($filter, $select, $replaceAlias);
    }
    

    /**
     * Retrieve minimal and maximal prices
     * 
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @return array (max, min)
     */
    public function _getMaxMinPrice($filter)
    {
        if (!$this->_maxMinPrice) {
            if ($this->_dataHelper->useSolr()) {
                $this->_computeMinMaxPriceFromSolr($filter);
            } else {
                $this->_computeMinMaxPriceFromDb($filter);
            }
        }
        return $this->_maxMinPrice;
    }

    /**
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     */
    protected function _computeMinMaxPriceFromSolr($filter)
    {
        /** @var Enterprise_Search_Model_Resource_Engine $engine */
        $engine = Mage::getModel('enterprise_search/resource_engine');

        /** @var Enterprise_Search_Model_Resource_Collection $productCollection */
        $productCollection = $filter->getLayer()->getProductCollection();
        $filters = $productCollection->getExtendedSearchParams();

        $queryText = $filters['query_text'];
        $query = $queryText ? $queryText : array('*' => '*');
        unset($filters['query_text']);

        $paramName = $engine->getSearchEngineFieldName('price');
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
        $this->_maxMinPrice = array($stats[$paramName]['max'], $stats[$paramName]['min']);
    }

    /**
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     */
    protected function _computeMinMaxPriceFromDb($filter)
    {
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();

        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::COLUMNS);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::ORDER);

        /* @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getResourceModel('catalog/product_collection');

        $priceExpression = $collection->getPriceExpression($select) . ' ' . $collection->getAdditionalPriceExpression($select);

        $select = $this->_removePriceFromSelect($select, $priceExpression);

        $sqlEndPart = ') * ' . $collection->getCurrencyRate() . ')';
        $select->columns('CEIL(MAX(' . str_replace('min', 'max', $priceExpression) . $sqlEndPart . ' as max_price');
        $select->columns('FLOOR(MIN(' . $priceExpression . $sqlEndPart . ' as min_price');
        $select->where($collection->getPriceExpression($select) . ' IS NOT NULL');

        $this->_maxMinPrice = $collection->getConnection()->fetchRow($select, array(), Zend_Db::FETCH_NUM);
    }
    

    /**
     * Retrieve maximal price
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @return float
     */
    public function getMaxPrice($filter)
    {
        $prices = $this->_getMaxMinPrice($filter);
        return $prices[0];
    }
    
    /**
     * Retrieve maximal price
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @return float
     */
    public function getMinPrice($filter)
    {
        $prices = $this->_getMaxMinPrice($filter);
        return $prices[1];
    }
    
    /**
     * Remove price records from where query
     * 
     * @param Varien_Db_Select $select
     * @param string $priceExpression
     * @return Varien_Db_Select
     */
    public function _removePriceFromSelect($select, $priceExpression)
    {
        $oldWhere = $select->getPart(Varien_Db_Select::WHERE);        
        $newWhere = array();
        foreach ($oldWhere as $cond) {
            if (false === strpos($cond, $priceExpression) && false === strpos($cond, str_replace('min', 'max', $priceExpression))) {
                   $newWhere[] = $cond;
            }
        }
        if ($newWhere && substr($newWhere[0], 0, 3) == 'AND') {
            $newWhere[0] = substr($newWhere[0], 3); 
        }                      
        $select->setPart(Varien_Db_Select::WHERE, $newWhere); 
        return $select; 
    }
    
    /**
     * Enter description here ...
     * @param Varien_Db_Select $select
     * @return string
     */
    public function getPriceExpression($select) 
    {
        $collection = Mage::getResourceModel('catalog/product_collection');      
        $priceExpression = $collection->getPriceExpression($select) . ' ' . $collection->getAdditionalPriceExpression($select);
        return  $priceExpression;
    }
    
    /**
     * Retrieve array with products counts per price range
     *
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @param array $ranges (23=>array(1,100), 24=>101-200)
     * @return array
     */
    public function getFromToCount($filter, $ranges)
    {
        $select = $this->_getSelect($filter);
        $countExpr  = new Zend_Db_Expr("COUNT(*)"); // may be add distinct ???
        $collection = Mage::getResourceModel('catalog/product_collection');
      
        $priceExpression = $this->getPriceExpression($select);
        
        $rangeExpr  = "CASE ";
        $price = $priceExpression;
        
        foreach($ranges as $n => $r) {
            $rangeExpr .= "WHEN ($price >= {$r[0]} AND $price < {$r[1]}) THEN $n ";
        }
        
        $rangeExpr .= " END";
        $rangeExpr = new Zend_Db_Expr($rangeExpr);

        $select->columns(array(
            'range' => $rangeExpr,
            'count' => $countExpr
        ));

        $select->group('range');
        
        return $this->_getReadAdapter()->fetchPairs($select);
    }

    public function applyPriceRange($filter)
    {
        $interval = $filter->getInterval();
        if (!$interval) {
            return $this;
        }

        list($from, $to) = $interval;
        if ($from === '' && $to === '') {
            return $this;
        }

        $select = $filter->getLayer()->getProductCollection()->getSelect();
        $priceExpr = $this->_getPriceExpression($filter, $select, false);

        if ($to !== '') {
            $to = (float)$to;
            if ($from == $to) {
                $to += self::MIN_POSSIBLE_PRICE;
            }
        }
        if ($from !== '') {
            $select->where(str_replace('min', 'max', $priceExpr) . ' >= ' . $this->_getComparingValue($from, $filter));
        }
        if ($to !== '') {
            $select->where($priceExpr . ' < ' . $this->_getComparingValue($to, $filter));
        }

        return $this;

    }
}
