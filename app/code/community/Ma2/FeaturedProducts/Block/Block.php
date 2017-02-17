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
/* $Id: Block.php 4 2013-11-05 07:31:07Z linhnt $ */

class Ma2_FeaturedProducts_Block_Block extends Mage_Catalog_Block_Product_Abstract {
    /*
     * Check sort option and limits set in System->Configuration and apply them
     * Additionally, set template to block so call from CMS will look like {{block type="featuredproducts/listing"}}
     */
    protected $params;
    
    public function __construct() {
        parent::__construct();
        $this->setTemplate('ma2_featuredproducts/block.phtml');
        $this->params = (array)Mage::getStoreConfig("featuredproducts/block");
    }
    
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
              ->setOrder($this->params['sort_by'], $this->params['sort_dir'])
              ;

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
            
            $collection->setPageSize($this->params['products_count'])
                        ->setCurPage(1);
            Mage::getModel('review/review')->appendSummary($collection);
            $collection->load();
                        
            $this->_productCollection = $collection;
        }
        
        return $this->_productCollection;
    }
    
    /*
     * Load featured products collection
     * */

    protected function _beforeToHtml() {
        
      $this->assign('FeaturedProductCollection',$this->_getProductCollection());
      $_columnCount = $this->params['column_count'];
      if(!$_columnCount || $_columnCount == 0 || empty($_columnCount)) $_columnCount = 3;
      $size_width = 100/$_columnCount;
      $this->assign('item_width',$size_width);
      $this->assign('_columnCount',(int)$_columnCount);
      
      /* assign variables in the parameters */
      foreach($this->params as $_para=>$value)
      {
        $this->assign($_para, $value);
      }
      
        return parent::_beforeToHtml();
    }

    protected function _toHtml() {

        if (!$this->helper('featuredproducts')->getIsActive()) {
            return '';
        }

        return parent::_toHtml();
    }

    /*
     * Return label for CMS block output
     * */

    protected function getBlockLabel() {
        return $this->helper('featuredproducts')->getCmsBlockLabel();
    }

}

?>