<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


class Amasty_Shopby_Model_Mysql4_Price extends Mage_Catalog_Model_Resource_Eav_Mysql4_Layer_Filter_Price
{
    protected $_price = null;

    /**
     * Initialize connection and define main table name
     *
     */
    protected function _construct()
    {
        parent::_construct();
    }
    
    /**
     * Retrieve minimal and maximal prices
     * 
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @return array (max, min)
     */
    public function getMaxMinPrice($filter) 
    {
        $hasPrice = $this->_price;
        $select = $this->_prepareSelect($filter, true);
        
        $price = $this->_price;
        $select->from('', "MAX($price) as max_price, MIN($price) as min_price");
        
        if (!$hasPrice)
            $this->_price = '';
            
        return $this->_getReadAdapter()->fetchRow($select, array(), Zend_Db::FETCH_NUM);
    }

    
    // default Magento ranges
    public function getCount($filter, $range)
    {
        $select = $this->_prepareSelect($filter, true);
        $price = $this->_price;
        $select->columns(array(
            'range' => new Zend_Db_Expr("FLOOR($price / $range) + 1"),
            'count' => new Zend_Db_Expr('COUNT(*)')
        ));
        $select->group('range');
        return $this->_getReadAdapter()->fetchPairs($select);
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
        $select = $this->_prepareSelect($filter, true);
        
        $countExpr  = new Zend_Db_Expr("COUNT(*)"); // may be add distinct ???
        
        $rangeExpr  = "CASE ";
        $price = $this->_price;
        foreach($ranges as $n=>$r)
            $rangeExpr .= "WHEN ($price >= {$r[0]} AND $price < {$r[1]}) THEN $n ";
        $rangeExpr .= " END";
        $rangeExpr = new Zend_Db_Expr($rangeExpr);

        $select->columns(array(
            'range' => $rangeExpr,
            'count' => $countExpr
        ));

        $select->group('range');

        return $this->_getReadAdapter()->fetchPairs($select);
    }

    /**
     * Apply custom filter
     */
    public function applyFromToFilter($filter, $from, $to)
    {
        $select = $this->_prepareSelect($filter);

        if ($from)
            $select->where($this->_price . ' >= ?', $from);
            
        if ($to)
            $select->where($this->_price . ' <= ?', $to);
            
        return $this;
    }
    
    protected function _prepareSelect($filter, $clone=false)
    {
        $collection = $filter->getLayer()->getProductCollection();
        $collection->addPriceData($filter->getCustomerGroupId(), $filter->getWebsiteId());
        $select     = $collection->getSelect();
       
        $ret  = $clone ? clone $select : $select;
        
        if ($this->_price){ 
            // we have already added all necessary joins and calculations
            // in the applyFromToFilter method
            // but need to remove FROM condition
            $oldWhere = $ret->getPart(Varien_Db_Select::WHERE);
            $newWhere = array();
            foreach ($oldWhere as $cond){
               if (false === strpos($cond, $this->_price))
                   $newWhere[] = $cond;
            }
            if ($newWhere && substr($newWhere[0], 0, 3) == 'AND')
               $newWhere[0] = substr($newWhere[0], 3); 
                      
            $ret->setPart(Varien_Db_Select::WHERE, $newWhere);             
        }
        else {
            $response   = $this->_dispatchPreparePriceEvent($filter, $ret);
    
            $table      = $this->_getIndexTableAlias();
            $additional = join('', $response->getAdditionalCalculations());
            $rate       = $filter->getCurrencyRate();
         
            // will be used in the count function
            $this->_price = "(({$table}.min_price {$additional}) * {$rate})";

        }

        if ($clone){
            $ret->reset(Zend_Db_Select::COLUMNS);
            $ret->reset(Zend_Db_Select::ORDER);
            $ret->reset(Zend_Db_Select::LIMIT_COUNT);
            $ret->reset(Zend_Db_Select::LIMIT_OFFSET);                   
        } 
        
        return $ret;
    }    
    
}