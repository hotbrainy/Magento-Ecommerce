<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
class Amasty_Shopby_Model_Mysql4_Value_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('amshopby/value');
    }
    
    public function addPositions()
    {
        if (empty($this->_map))
            $this->_map = array();
            
        $this->_map['fields']['option_id'] = 'main_table.option_id';
        
        $this->getSelect()->joinInner(
            array('o'=> $this->getTable('eav/attribute_option')), 
            'main_table.option_id = o.option_id', 
            array('o.sort_order')
        );
            
        return $this;
    }

    public function addValue()
    {
        $storeId = Mage::app()->getStore()->getId();

        $this->getSelect()->joinLeft(
            array('ov1' => $this->getTable('eav/attribute_option_value')),
            'main_table.option_id = ov1.option_id AND ov1.store_id=0',
            array()
        );

        $this->getSelect()->joinLeft(
            array('ov2' => $this->getTable('eav/attribute_option_value')),
            'main_table.option_id = ov2.option_id AND ov2.store_id=' . $storeId,
            array('value', new Zend_Db_Expr('IF(ov2.value_id IS NULL, ov1.value, ov2.value)'))
        );

        return $this;
    }
}