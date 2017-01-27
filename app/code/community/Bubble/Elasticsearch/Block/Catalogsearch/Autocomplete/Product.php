<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
/**
 * @method  Mage_Catalog_Model_Product  getEntity()
 * @method  $this                       setEntity(Mage_Catalog_Model_Product $product)
 */
class Bubble_Elasticsearch_Block_Catalogsearch_Autocomplete_Product
    extends Bubble_Elasticsearch_Block_Catalogsearch_Result
{
    /**
     * @var Mage_Core_Block_Template
     */
    protected $_priceBlock;

    /**
     * @var string
     */
    protected $_autocompleteTitle = 'Products';

    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('bubble/elasticsearch/autocomplete/product.phtml');
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getProductUrl(Mage_Catalog_Model_Product $product)
    {
        return $product->getProductUrl();
    }

    /**
     * Returns price HTML of given product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getPriceHtml(Mage_Catalog_Model_Product $product)
    {
        return $this->getPriceBlock()
            ->setUseLinkForAsLowAs(false)
            ->getPriceHtml($product, true);
    }

    /**
     * Returns price block
     *
     * @return Mage_Core_Block_Template
     */
    public function getPriceBlock()
    {
        if (null === $this->_priceBlock) {
            $this->_priceBlock = $this->getLayout()->createBlock('elasticsearch/catalog_product_price');
        }

        return $this->_priceBlock;
    }

    /**
     * @return Mage_Catalog_Helper_Image
     */
    public function getImageSrc()
    {
        return Mage::helper('catalog/image')->init($this->getEntity(), 'thumbnail')->resize($this->getImageSize());
    }

    /**
     * Returns configured image size
     *
     * @return int
     */
    public function getImageSize()
    {
        return (int) Mage::getStoreConfig('elasticsearch/product/image_size');
    }
}