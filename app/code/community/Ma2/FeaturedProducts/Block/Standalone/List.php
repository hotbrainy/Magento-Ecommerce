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
/* $Id: List.php 4 2013-11-05 07:31:07Z linhnt $ */

class Ma2_FeaturedProducts_Block_Standalone_List extends Mage_Catalog_Block_Product_List
{
	protected $_productCollection;
  protected $params;
    
  public function __construct() {
    parent::__construct();
    $this->params = (array)Mage::getStoreConfig("featuredproducts/standalone");
  }
  
  protected function _prepareLayout()
  {
    if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
      $breadcrumbsBlock->addCrumb('home', array(
        'label'=>Mage::helper('catalog')->__('Home'),
        'title'=>Mage::helper('catalog')->__('Go to Home Page'),
        'link'=>Mage::getBaseUrl()
      ));
    }    
    
    parent::_prepareLayout();
  }
  
  
	/*
	 * Load featured products collection
	 * */
	protected function _getProductCollection()
	{
    
		if (is_null($this->_productCollection)) {
      $collection = Mage::getModel('catalog/product')->getCollection();
      $attributes = Mage::getSingleton('catalog/config')
        ->getProductAttributes();
      
      $storeId = Mage::app()->getStore()->getId();
      
      $limit = (int)$this->getRequest()->getParam('limit') ? (int)$this->getRequest()->getParam('limit') : (int)$this->getToolbarBlock()->getDefaultPerPageValue();
      
      $collection->addAttributeToSelect($attributes)
        ->addMinimalPrice()
        ->addFinalPrice()
        ->addTaxPercents()
        ->addAttributeToFilter('status', 1)
        ->addAttributeToFilter('ma2_featured_product', 1)
        ->addStoreFilter($storeId)
        ->setOrder($this->getRequest()->getParam('order'), $this->getRequest()->getParam('dir'))
        ;

      Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
      Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
      
      $collection->setPageSize($limit)
                  ->setCurPage($this->getRequest()->getParam('p'));
      Mage::getModel('review/review')->appendSummary($collection);
      $collection->load();
      
      $this->_productCollection = $collection;
    }
		return $this->_productCollection;
  }

   
	/*
	 * Remove "Position" option from Sort By dropdown
	 * Assign product and params
   */
	protected function _beforeToHtml()
	{
		parent::_beforeToHtml();
		$toolbar = $this->getToolbarBlock();
		$toolbar->removeOrderFromAvailableOrders('position');
    
    if (!$this->_getProductCollection()->count()){
			return '';
		}
    
    $this->assign('FeaturedProductCollection', $this->_getProductCollection());
    $_columnCount = (int)$this->params['column_count'];
    if(!$_columnCount || $_columnCount == 0 || empty($_columnCount)) $_columnCount = 3;
    $size_width = 100/$_columnCount;
    $this->assign('item_width',$size_width);
    $this->assign('_columnCount', $_columnCount);
    $this->setData('column_count', $_columnCount);
    /* assign variables in the parameters */
    foreach($this->params as $_para=>$value)
    {
      $this->assign($_para, $value);
    }
    
		return $this;
	}
  
  

}
?>