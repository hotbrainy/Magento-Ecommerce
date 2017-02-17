<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Rules_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('mageworx_customercredit/rules');
    }
    
    public function setRuleTypeFilter($type=2){
        $this->getSelect()->where("rule_type=?",$type);
        return $this;
    }

    public function setValidationFilter($websiteId, $customerGroupId)
    {
        $this->getSelect()->where('is_active=1');
        $this->getSelect()->where('find_in_set(?, website_ids)', (int)$websiteId);
        $this->getSelect()->where('find_in_set(?, customer_group_ids)', (int)$customerGroupId);
     	return $this;
    }

    public function setValidationByCustomerGroup($customerGroupId)
    {
        $this->getSelect()->where('is_active=1');
        $this->getSelect()->where('find_in_set(?, customer_group_ids)', (int)$customerGroupId);
     	return $this;
    }
    
    public function addCounts()
    {
        $this->getSelect()
                ->joinLeft(array('log'=>$this->getTable('mageworx_customercredit/credit_log')), 'main_table.rule_id=log.rule_id', array('count_rule'=>'COUNT(log.log_id)'))
                ->group('main_table.rule_id');
        return $this;
    }
    /**
     * Filter collection by specified website IDs
     *
     * @param int|array $websiteIds
     * @return Mage_CatalogRule_Model_Mysql4_Rule_Collection
     */
    public function addWebsiteFilter($websiteIds)
    {
        if (!is_array($websiteIds)) {
            $websiteIds = array($websiteIds);
        }
        $parts = array();
        foreach ($websiteIds as $websiteId) {
            $parts[] = $this->getConnection()->quoteInto('FIND_IN_SET(?, main_table.website_ids)', $websiteId);
        }
        if ($parts) {
            $this->getSelect()->where(new Zend_Db_Expr(implode(' OR ', $parts)));
        }
        return $this;
    }
}