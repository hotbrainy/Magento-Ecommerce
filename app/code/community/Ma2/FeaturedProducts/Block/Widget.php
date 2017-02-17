<?php
/**
 * MagenMarket.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Edit or modify this file with yourown risk.
 *
 * @category    Extensions
 * @package     Ma2_FeaturedProducts
 * @copyright   Copyright (c) 2013 MagenMarket. (http://www.magenmarket.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
**/
/* $Id: Widget.php 4 2013-11-05 07:31:07Z linhnt $ */
?>
<?php
class Ma2_FeaturedProducts_Block_Widget extends Mage_Catalog_Block_Product_Abstract 
implements Mage_Widget_Block_Interface
{

    /**
     * Get products collection
     */
    
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $collection = Mage::getModel('catalog/product')->getCollection();
            $attributes = Mage::getSingleton('catalog/config')
              ->getProductAttributes();
            
            $storeId = Mage::app()->getStore()->getId();
            
            $collection->addAttributeToSelect($attributes)
              ->addMinimalPrice()
              ->addFinalPrice()
              ->addTaxPercents()
              ->addAttributeToFilter('status', 1)
              ->addAttributeToFilter('ma2_featured_product', 1)
              ->addStoreFilter($storeId)
              ->setOrder($this->getData('sort_by'), $this->getData('sort_dir'))
              ;

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
            
            $collection->setPageSize($this->getData('products_count'))
                        ->setCurPage(1);
            Mage::getModel('review/review')->appendSummary($collection);
            $collection->load();
                        
            $this->_productCollection = $collection;
        }
        
        return $this->_productCollection;
    }
    
    
    /**
     *  To html
     *	Assign variables
     */
    protected function _toHtml()
    {
      $this->assign('WidgetProductProductCollection',$this->_getProductCollection());
      $_columnCount = $this->getData('column_count');
      if(!$_columnCount || $_columnCount == 0 || empty($_columnCount)) $_columnCount = 3;
      $size_width = 100/$_columnCount;
      $this->assign('item_width',$size_width);
      $this->assign('_columnCount',(int)$_columnCount);
      /* assign variables in the parameters */
      foreach($this->getData() as $_para=>$value)
      {
        $this->assign($_para, $value);
      }
      return parent::_toHtml();
    }

}