<?php
/**
 * Display suggestions in catalog search results
 *
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Block_Catalogsearch_Result extends Mage_Core_Block_Template
{
    /**
     * @var Bubble_Elasticsearch_Helper_Data
     */
    protected $_helper;

    /**
     * @var string
     */
    protected $_autocompleteTitle = '';

    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_helper = Mage::helper('elasticsearch');
    }

    /**
     * Retrieve categories matching text query
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getCategoryCollection()
    {
        return $this->_helper->getCategoryCollection($this->getQueryText());
    }

    /**
     * Return given category path name with specified separator
     *
     * @param Mage_Catalog_Model_Category $category
     * @param string $separator
     * @return string
     */
    public function getCategoryPathName(Mage_Catalog_Model_Category $category, $separator = ' > ')
    {
        if ($this->_helper->getShowCategoryPath()) {
            return Mage::helper('elasticsearch/indexer_category')->getCategoryPathName($category, $separator);
        }

        return $category->getName();
    }

    /**
     * Retrieve CMS pages matching text query
     *
     * @return Mage_Cms_Model_Resource_Page_Collection
     */
    public function getPageCollection()
    {
        return $this->_helper->getPageCollection($this->getQueryText());
    }

    /**
     * Returns current text query
     *
     * @return string
     */
    public function getQueryText()
    {
        return $this->helper('catalogsearch')->getQueryText();
    }

    /**
     * Returns translated label if available
     *
     * @param string $label
     * @return string
     */
    public function getLabel($label)
    {
        $labels = Mage::helper('elasticsearch/autocomplete')->getLabels();
        if (isset($labels[$label]) && '' !== trim($labels[$label])) {
            $label = trim($labels[$label]);
        }

        return $label;
    }

    /**
     * Should be overriden in child classes
     *
     * @return string
     */
    public function getAutocompleteTitle()
    {
        return $this->getLabel($this->_autocompleteTitle);
    }
}