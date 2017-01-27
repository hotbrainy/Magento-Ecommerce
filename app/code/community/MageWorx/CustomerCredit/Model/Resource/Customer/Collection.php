<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Resource_Customer_Collection extends Mage_Customer_Model_Resource_Customer_Collection
{
    /**
     * Adding item to item array
     *
     * @param   Varien_Object $item
     * @return  Varien_Data_Collection
     */
    public function addItem(Varien_Object $item)
    {
        $itemId = $this->_getItemId($item);
        if (!is_null($itemId)) {
            $this->_items[] = $item;
        } else {
            $this->_addItem($item);
        }
        return $this;
    }
}
