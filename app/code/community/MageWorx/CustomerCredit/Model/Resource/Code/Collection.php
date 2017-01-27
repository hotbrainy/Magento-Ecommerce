<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Code_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init('mageworx_customercredit/code');
	}
	
	/**
	 * Filter collection by websites
	 * 
	 * @param int|array $id 
	 * @return MageWorx_CustomerCredit_Model_Resource_Code_Collection
	 */
	public function addWebsiteFilter($id)
	{
		$this->addFieldToFilter('website_id', array('in'=>$id));
        return $this;
	}
        
        public function addOwnerFilter($owner_id)
        {
            $this->addFieldToFilter('owner_id',$owner_id);
            return $this;
        }
        
        public function onlyUnused()
        {
            $this->getSelect()->where('used_date IS NULL');
            return $this;
        }
}