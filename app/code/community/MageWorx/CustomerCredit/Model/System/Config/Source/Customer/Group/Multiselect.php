<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_System_Config_Source_Customer_Group_Multiselect
{
    /**
     * Customer groups options array
     *
     * @var null|array
     */
    protected $_options;

    /**
     * Retrieve customer groups as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('customer/group_collection')
                ->setRealGroupsFilter()
                ->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
