<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Block_Autocomplete_Product extends Bubble_Elasticsearch_Block_Autocomplete_Abstract
{
    /**
     * @var string
     */
    protected $_title = 'Products';

    /**
     * @var string
     */
    protected $_template = 'bubble/elasticsearch/autocomplete/product.phtml';

    /**
     * @param Varien_Object $product
     * @return string
     */
    public function getProductUrl(Varien_Object $product)
    {
        return $this->cleanUrl($product->getData('_url'));
    }

    /**
     * @return string
     */
    public function getImageSrc()
    {
        return $this->cleanUrl($this->getEntity()->getData('image'));
    }

    /**
     * @return int
     */
    public function getImageSize()
    {
        return $this->_config ? $this->_config->getConfig('product/image_size') : 50;
    }

    /**
     * Returns price HTML of given product
     *
     * @param Varien_Object $product
     * @return string
     */
    public function getPriceHtml(Varien_Object $product)
    {
        $block = new Bubble_Elasticsearch_Block_Autocomplete_Product_Price;
        $block->setEntity($product);
        $block->setConfig($this->_config);

        return $block->toHtml();
    }

    /**
     * @see Mage_Catalog_Model_Product_Visibility
     * @param array $data
     * @return bool
     */
    public function validate($data)
    {
        return isset($data['_prices']) && isset($data['visibility']) && $data['visibility'] >= 3;
    }
}