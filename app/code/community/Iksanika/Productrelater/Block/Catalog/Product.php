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

class Iksanika_Productrelater_Block_Catalog_Product extends Mage_Adminhtml_Block_Catalog_Product
{
    
    public function __construct()
    {
        parent::__construct();
        $this->_headerText = Mage::helper('productrelater')->__('Mass Product Relater');
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('iksanika/productrelater/catalog/product.phtml');
        $this->setChild('grid', $this->getLayout()->createBlock('productrelater/catalog_product_grid', 'product.productrelater'));
    }
}