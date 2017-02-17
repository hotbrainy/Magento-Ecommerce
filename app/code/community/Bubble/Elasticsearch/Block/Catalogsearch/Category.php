<?php
/**
 * Display suggestions in catalog search results
 *
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Block_Catalogsearch_Category extends Bubble_Elasticsearch_Block_Catalogsearch_Result
{
    /**
     * @var Mage_Catalog_Model_Resource_Category_Collection
     */
    protected $_categories;

    /**
     * Retrieve categories matching text query
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getCategoryCollection()
    {
        if (!$this->_helper->isSearchEnabled('category')) {
            return new Varien_Data_Collection(); // empty collection
        }

        if (!$this->_categories) {
            $this->_categories = parent::getCategoryCollection();
        }

        if ($limit = $this->getLimit()) {
            $this->_categories->getSelect()->limit($limit);
        }

        return $this->_categories;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return (int) Mage::getStoreConfig('elasticsearch/category/limit');
    }
}