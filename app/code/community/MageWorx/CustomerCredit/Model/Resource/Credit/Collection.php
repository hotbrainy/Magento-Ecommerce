<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Credit_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
            $this->_init('mageworx_customercredit/credit');
    }

    /**
     * Filter collection by websites
     * 
     * @param int|array $id 
     * @return MageWorx_CustomerCredit_Model_Resource_Credit_Collection
     */
    public function addWebsiteFilter($id)
    {
        $this->addFieldToFilter('website_id', array('in'=>$id));
        return $this;
    }
	
    /**
     * Filter collection by customers
     * 
     * @param int|array $id 
     * @return MageWorx_CustomerCredit_Model_Resource_Credit_Collection
     */
    public function addCustomerFilter($id)
    {
        $this->addFieldToFilter('customer_id', array('in'=>$id));
        return $this;
    }
    
    public function joinCustomerTable() {
        $this->getSelect()
             ->join(array('c'=>$this->getTable('customer/entity')), 'c.entity_id=main_table.customer_id', array('group_id'));
        return $this;     
    }
}