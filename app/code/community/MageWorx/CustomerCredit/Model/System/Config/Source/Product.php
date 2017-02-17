<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_System_Config_Source_Product extends Mage_Core_Model_Config_Data
{    
    
    public function toOptionArray() {
       
        $options = array(
            array('value'=>'', 'label'=>Mage::helper('mageworx_customercredit')->__('None'))
        );
                
        $collection = Mage::getResourceModel('catalog/product_collection')            
            ->setStoreId(Mage::app()->getStore()->getId())
            ->addStoreFilter(Mage::app()->getStore()->getId())
            ->addAttributeToFilter('type_id', 'virtual')
            ->addAttributeToSelect('name')
            ->setOrder('name', $dir='asc');
                
        if (count($collection)>0) {
            foreach ($collection as $product) {
                if ($product->getSku() && $product->getName()) {
                    $options[] = array('value'=>$product->getSku(), 'label'=>$product->getName());
                }
            }
        }     
        return $options;
    }    
    
}