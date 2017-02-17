<?php
/**
 * MageWorx
 * All Extension
 *
 * @category   MageWorx
 * @package    MageWorx_All
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

abstract class MageWorx_All_Model_System_Config_Source_Cms_AbstractSource
{
    protected $options;

    abstract protected function getModel();

    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->options) {
            $collection = $this->getModel()->getCollection();
            $collection->addFieldToFilter('is_active', array('eq' => 1));
            if (!Mage::app()->isSingleStoreMode()) {
                $collection->addStoreFilter($this->getConfigStore());
            }

            $this->options = $collection->loadData()->toOptionArray(false);
        }

        $options = $this->options;
        if(!$isMultiselect){
            array_unshift($options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
        }

        return $options;
    }
    
    /**
     * Get configuration store. 
     * Website is not checked. Configuration settings which use this class as 
     * source model should not be visible in Website scope.
     *
     * @return int|Mage_Core_Model_Store Selected store or "All Stores" (Default Config)
     */
    protected function getConfigStore()
    {
        $storeCode = Mage::app()->getRequest()->getParam('store');

        $store = 0;
        if ($storeCode) {
            $store = Mage::app()->getStore($storeCode);
        }
        
        return $store;
    }
}