<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

// todo merge with 14? 
class Amasty_Shopby_Model_Mysql4_Price13 extends Mage_CatalogIndex_Model_Mysql4_Price
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
     * @return array (max, min)
     */
    public function getMaxMinPrice($attribute = null, $entitySelect) 
    {
        $select = $this->_prepareSelect(null, true, $entitySelect, $attribute);
        $price  = $this->_price;
        
        $select->from('', "MAX($price) as max_price, MIN($price) as min_price");
            
        return $this->_getReadAdapter()->fetchRow($select, array(), Zend_Db::FETCH_NUM);
    }
    
    // compatibility with default magento ranges
    // NOTE! DIFFERENT argumens order (magento bug)
    public function getCount($attribute, $range, $entitySelect)
    {
        $this->_price = ''; //important
        $select = $this->_prepareSelect(null, true, $entitySelect, $attribute);
        $price  = $this->_price;
        
        $select->columns(array(
            'range' => new Zend_Db_Expr("FLOOR({$price} / {$range}) + 1"),
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
        // it already contains all necessary joins
        $select = $this->_prepareSelect($filter, true);

        $countExpr  = new Zend_Db_Expr('COUNT(DISTINCT price_table_amshopby.entity_id)');
        
        $rangeExpr  = "CASE ";
        $price    =  $this->_price;
        
        
        foreach ($ranges as $n => $r){
            $rangeExpr .= "WHEN ($price >= {$r[0]} AND $price < {$r[1]}) THEN $n ";
        }
        $rangeExpr .= " END";
        $rangeExpr = new Zend_Db_Expr($rangeExpr);

        $select->from('', array('range' => $rangeExpr, 'count' => $countExpr))->group('range');
        
        $counts = $this->_getReadAdapter()->fetchPairs($select);
        return $counts;
    }

    /**
     * Apply attribute filter to product collection
     * @param Mage_Catalog_Model_Layer_Filter_Price $filter
     * @param float $from
     * @param float $to
     */
    public function applyFromToFilter($filter, $from, $to)
    {
        $select = $this->_prepareSelect($filter);
        
        if ($from){
            $select->where($this->_price . ' >= ?', $from);
        }
            
        if ($to){
            $select->where($this->_price . ' <= ?', $to);
        }

        return $this;  
    }
    
    protected function _prepareSelect($filter, $clone=false, $baseSelect = null, $attribute=null)
    {
        if ($baseSelect) { // we need for 1.3 compatibility
            $select = $baseSelect;
        }
        else {
            $collection = $filter->getLayer()->getProductCollection();
            $select     = $collection->getSelect(); 
            $attribute  =  $filter->getAttributeModel();          
        }
        
        $ret  = $clone ? clone $select : $select;

        if ($this->_price){ // after apply() function or getMaxInt()
            $oldWhere = $ret->getPart(Varien_Db_Select::WHERE);
            $newWhere = array();
            foreach ($oldWhere as $cond){
               if (false === strpos($cond, $this->_price)){
                   $newWhere[] = $cond;
               }
            }
            if ($newWhere && substr($newWhere[0], 0, 3) == 'AND'){
               $newWhere[0] = substr($newWhere[0], 3); 
            }
                      
            $ret->setPart(Varien_Db_Select::WHERE, $newWhere);             
        }
        else{ //first time
            $ret->distinct(true);
            
            $tableName = 'price_table_amshopby';
            $ret->joinLeft(
                array($tableName => $this->getMainTable()),
                $tableName .'.entity_id=e.entity_id',
                array()
            );
    
            $response = new Varien_Object();
            $response->setAdditionalCalculations(array());
    
            $ret
                ->where($tableName . '.website_id = ?', $this->getWebsiteId())
                ->where($tableName . '.attribute_id = ?', $attribute->getId());
    
            if ($attribute->getAttributeCode() == 'price') {
                $ret->where($tableName . '.customer_group_id = ?', $this->getCustomerGroupId());
                $args = array(
                    'select'         => $ret,
                    'table'          => $tableName,
                    'store_id'       => $this->getStoreId(),
                    'response_object'=> $response,
                );
                Mage::dispatchEvent('catalogindex_prepare_price_select', $args);
            }
            
            $additional = join('', $response->getAdditionalCalculations());
            // will be used in the count function
            $this->_price = "(({$tableName}.value {$additional}) * {$this->getRate()})";
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