<?php
/**
 * Display suggestions in catalog search results
 *
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Block_Catalogsearch_Cms extends Bubble_Elasticsearch_Block_Catalogsearch_Result
{
    /**
     * @var Mage_Cms_Model_Resource_Page_Collection
     */
    protected $_pages;

    /**
     * Retrieve CMS pages matching text query
     *
     * @return Mage_Cms_Model_Resource_Page_Collection
     */
    public function getPageCollection()
    {
        if (!$this->_helper->isSearchEnabled('cms')) {
            return new Varien_Data_Collection(); // empty collection
        }

        if (!$this->_pages) {
            $this->_pages = parent::getPageCollection();
        }

        if ($limit = $this->getLimit()) {
            $this->_pages->getSelect()->limit($limit);
        }

        return $this->_pages;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return (int) Mage::getStoreConfig('elasticsearch/cms/limit');
    }
}