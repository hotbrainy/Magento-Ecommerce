<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */ 
class Amasty_Shopby_Model_Mysql4_CatalogIndex_Data_Configurable extends Mage_CatalogIndex_Model_Mysql4_Data_Configurable 
{
    public function fetchLinkInformation($store, $table, $idField, $whereField, $id, $additionalWheres = array()) 
    {
        $productIds = parent::fetchLinkInformation($store, $table, $idField, $whereField, $id, $additionalWheres);

        $showInStockOnly = Mage::getStoreConfig('amshopby/general/show_in_stock', $store); 
        
        if ($showInStockOnly){
            // hash of id=>status;
            $productStatus = Mage::getModel('cataloginventory/stock_status')->getProductStatus(
                $productIds,
                Mage::app()->getStore($store)->getWebsiteId()
            );
            
            $newIds = array();
            foreach ($productIds as $id) {
                if (Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK == $productStatus[$id]) {
                    $newIds[] = $id;
                }
            }
            
            $productIds = $newIds;
        }
        
        return $productIds;
    }
}