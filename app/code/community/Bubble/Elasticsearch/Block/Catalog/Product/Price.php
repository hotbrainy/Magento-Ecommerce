<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Block_Catalog_Product_Price extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addPriceBlockType('bundle', 'bundle/catalog_product_price', 'bundle/catalog/product/price.phtml');
    }

    /**
     * @param bool $bool
     * @return $this
     */
    public function setUseLinkForAsLowAs($bool = true)
    {
        $this->_useLinkForAsLowAs = $bool;

        return $this;
    }
}