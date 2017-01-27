<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Code_Log_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected function _construct()
	{
		$this->_init('mageworx_customercredit/code_log');
	}
	
	/**
     * Filter collection by codes
     * 
     * @param int|array $id 
     * @return MageWorx_CustomerCredit_Model_Resource_Code_Log_Collection
     */
	public function addCodeFilter($id)
	{
	    $this->addFieldToFilter('code_id', array('in'=>$id));
        return $this;
	}
}