<?php

/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Productrelater
 * @copyright  Copyright (c) 2013 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */

include_once "Mage/Adminhtml/controllers/Catalog/ProductController.php";

class Iksanika_Productrelater_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController 
{ 
    protected function _construct() 
    { 
        $this->setUsedModuleName('Iksanika_Productrelater'); 
    } 
    
    public function indexAction() 
    { 
        $this->loadLayout(); 
        $this->_setActiveMenu('catalog/productrelater'); 
        $this->_addContent($this->getLayout()->createBlock('productrelater/catalog_product')); 
        $this->renderLayout(); 
    } 
    
    public function gridAction() 
    { 
        $this->loadLayout(); 
        $this->getResponse()->setBody($this->getLayout()->createBlock('productrelater/catalog_product_grid')->toHtml()); 
    } 
    
    protected function _isAllowed() 
    { 
        return Mage::getSingleton('admin/session')->isAllowed('catalog/products'); 
    } 

    public function massUpdateProductsAction()
    {
        $productIds = $this->getRequest()->getParam('product');

        if (is_array($productIds)) 
        {
            try {
                
                foreach ($productIds as $itemId => $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $productBefore = $product;

                    // event was not dispached by some reasons ??? so the code to prove product is below
                    // if ($this->massactionEventDispatchEnabled)
                    //    Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    
                    $columnForUpdate = array('related_ids', 'cross_sell_ids', 'up_sell_ids');
                    
                    foreach($columnForUpdate as $columnName)
                    {
                        $columnValuesForUpdate = $this->getRequest()->getParam($columnName);
                        // handle exceptional situation or related tables savings
                        if($columnName == 'related_ids')
                        {
                            $relatedIds = trim($columnValuesForUpdate[$itemId]) != "" ? explode(',', trim($columnValuesForUpdate[$itemId])) : array();
                            $link = $this->getRelatedLinks($relatedIds, array(), $productId);
                            $product->setRelatedLinkData($link);
                        }else
                        if($columnName == 'cross_sell_ids')
                        {
                            $crossSellIds = trim($columnValuesForUpdate[$itemId]) != "" ? explode(',', trim($columnValuesForUpdate[$itemId])) : array();
                            $link = $this->getRelatedLinks($crossSellIds, array(), $productId);
                            $product->setCrossSellLinkData($link);
                        }else
                        if($columnName == 'up_sell_ids')
                        {
                            $upSellIds = trim($columnValuesForUpdate[$itemId]) != "" ? explode(',', trim($columnValuesForUpdate[$itemId])) : array();
                            $link = $this->getRelatedLinks($upSellIds, array(), $productId);
                            $product->setUpSellLinkData($link);
                        }
                    }
                    $product->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully refreshed.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index');
    }
    
    
    
    public function getRelatedLinks($productIds, $existProducts, $productId)
    {
        $link = array();
        foreach ($productIds as $relatedToId) 
        {
            if ($productId != $relatedToId) 
            {
                $link[$relatedToId] = array('position' => null);
            }
        }
        // Fetch and append to already related products.
        foreach($existProducts as $existProduct)
        {
            $link[$existProduct->getId()] = array('position' => null);
        }
        return $link;
    }
    
     /**************************************************************************
     ** MAKE PRODUCTS RELATED
     **************************************************************************/
    
    
    /** 
     * Action make cheched products list related to each other.
     **/     
    public function massRelatedEachOtherAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $link = $this->getRelatedLinks($productIds, $product->getRelatedProducts(), $productId);
                    $product->setRelatedLinkData($link);
                    
                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully related to each other.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index');
    }
    
    
    
    /** 
     * Action which make selected products to specified products list (IDs)
     **/     
    public function massRelatedToAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $productIds2List = $this->getRequest()->getParam('callbackval');
        $productIds2 = explode(',', $productIds2List);
        
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $link = $this->getRelatedLinks($productIds2, $product->getRelatedProducts(), $productId);
                    $product->setRelatedLinkData($link);

                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully related to products('.$productIds2List.').', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index');
    }
    
    
    
    /**
     * Action remove all relation in checked products list.
     **/     
    public function massRelatedCleanAction() 
    {
        $productIds = $this->getRequest()->getParam('product');
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $product->setRelatedLinkData(array());
                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) are no longer related to any other products.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index');
    }

    
    
    
    
    
     
    /***************************************************************************
     ** Cross-Selling
     **************************************************************************/  
    
    
    /** 
     * This will cross sell all products with each other.     
     **/  
    public function massCrossSellEachOtherAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $link = $this->getRelatedLinks($productIds, $product->getCrossSellProducts(), $productId);
                    $product->setCrossSellLinkData($link);

                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully cross-related to each other.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index');
    }
    
    
    
    /** 
     * This will relate all products to a specifc list of products 
     **/     
    public function massCrossSellToAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $productIds2List = $this->getRequest()->getParam('callbackval');
        $productIds2 = explode(',', $productIds2List);
        
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $link = $this->getRelatedLinks($productIds2, $product->getCrossSellProducts(), $productId);
                    $product->setCrossSellLinkData($link);
                    
                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) were successfully set as cross-sells by products('.$productIds2List.').', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index');
    }
    
    
    /**
     * This will unrelate related product's relations.
     **/     
    public function massCrossSellClearAction() 
    {
        $productIds = $this->getRequest()->getParam('product');
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $product->setCrossSellLinkData(array());
                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) now have no products as cross sell links.', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index');
    }
    
    
    
    
    
    /***************************************************************************
     ** Up-Selling
     **************************************************************************/  
    
    
    /** 
     * This will relate all products to a specifc list of products 
     **/
    public function massUpSellToAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        $productIds2List = $this->getRequest()->getParam('callbackval');
        $productIds2 = explode(',', $productIds2List);
        
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $link = $this->getRelatedLinks($productIds2, $product->getUpSellProducts(), $productId);
                    $product->setUpSellLinkData($link);
                    
                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) are now up-sold by products('.$productIds2List.').', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index');
    }
    
    
    
    /**
     * This will unrelate related product's relations.
     **/
    public function massUpSellClearAction() 
    {
        $productIds = $this->getRequest()->getParam('product');
        if (is_array($productIds)) 
        {
            try {
                foreach ($productIds as $productId) 
                {
                    $product = Mage::getModel('catalog/product')->load($productId);
                    $product->setUpSellLinkData(array());
                    if ($this->massactionEventDispatchEnabled)
                    {
                        Mage::dispatchEvent('catalog_product_prepare_save', array('product' => $product, 'request' => $this->getRequest()));
                    }
                    $product->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d record(s) now have 0 up-sells', count($productIds)));
            } catch (Exception $e) 
            {
                $this->_getSession()->addError($e->getMessage());
            }
        }else
        {
            $this->_getSession()->addError($this->__('Please select product(s)').'. '.$this->__('You should select checkboxes for each product row which should be updated. You can click on checkboxes or use CTRL+Click on product row which should be selected.'));
        }
        $this->_redirect('*/*/index');
    }

}