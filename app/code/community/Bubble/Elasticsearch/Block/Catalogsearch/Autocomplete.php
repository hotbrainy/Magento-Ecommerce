<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Block_Catalogsearch_Autocomplete extends Bubble_Elasticsearch_Block_Catalogsearch_Result
{
    /**
     * @var Bubble_Elasticsearch_Helper_Autocomplete
     */
    protected $_helper;

    /**
     * @var Mage_Catalog_Model_Resource_Category_Collection
     */
    protected $_categories;

    /**
     * @var Mage_Cms_Model_Resource_Page_Collection
     */
    protected $_pages;

    /**
     * @var Mage_Catalog_Model_Resource_Product_Collection
     */
    protected $_products;

    /**
     * @var array
     */
    protected $_entityBlocks = array();

    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('bubble/elasticsearch/autocomplete.phtml');
        $this->_helper = Mage::helper('elasticsearch/autocomplete');
    }

    /**
     * Forward to default autocomplete if Elasticsearch autocomplete is disabled
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_helper->isGlobalAutocompleteEnabled()) {
            $block = new Mage_CatalogSearch_Block_Autocomplete();

            return $block->toHtml();
        }

        return parent::_toHtml();
    }

    /**
     * Retrieve categories matching text query
     *
     * @return Mage_Catalog_Model_Resource_Category_Collection
     */
    public function getCategoryCollection()
    {
        if (!$this->_helper->isAutocompleteEnabled('category')) {
            return new Varien_Data_Collection(); // empty collection
        }

        if (!$this->_categories) {
            $collection = parent::getCategoryCollection();

            if ($limit = $this->getLimit()) {
                $collection->getSelect()->limit($limit);
            }

            $this->_categories = $collection;
        }

        return $this->_categories;
    }

    /**
     * Retrieve CMS pages matching text query
     *
     * @return Mage_Cms_Model_Resource_Page_Collection
     */
    public function getPageCollection()
    {
        if (!$this->_helper->isAutocompleteEnabled('cms')) {
            return new Varien_Data_Collection(); // empty collection
        }

        if (!$this->_pages) {
            $collection = parent::getPageCollection();

            if ($limit = $this->getLimit()) {
                $collection->getSelect()->limit($limit);
            }

            $this->_pages = $collection;
        }

        return $this->_pages;
    }

    /**
     * Retrieve products matching text query
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        if (!$this->_products) {
            $query = $this->getQueryText();
            $queryObject = Mage::helper('catalogsearch')->getQuery();
            if ($queryObject->getSynonymFor()) {
                $query = $queryObject->getSynonymFor();
            }
            $collection = $this->_helper->getProductCollection($query);

            $collection->addAttributeToSelect($this->_helper->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addUrlRewrite()
                ->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInSearchIds())
                ->addAttributeToFilter('status',
                    array('in' => Mage::getSingleton('catalog/product_status')->getVisibleStatusIds())
                );

            if ($limit = $this->getLimit()) {
                $collection->getSelect()->limit($limit);
            }

            $this->_products = $collection;
        }

        return $this->_products;
    }

    /**
     * Returns category block
     *
     * @return Bubble_Elasticsearch_Block_Catalogsearch_Autocomplete_Category
     */
    public function getCategoryBlock()
    {
        if (null === $this->_categoryBlock) {
            $this->_categoryBlock = $this->getLayout()
                ->createBlock('elasticsearch/catalogsearch_autocomplete_category');
        }

        return $this->_categoryBlock;
    }

    /**
     * Returns category HTML
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryHtml(Mage_Catalog_Model_Category $category)
    {
        return $this->getCategoryBlock()->setCategory($category)->toHtml();
    }

    /**
     * Returns CMS page block
     *
     * @return Bubble_Elasticsearch_Block_Catalogsearch_Autocomplete_Cms
     */
    public function getPageBlock()
    {
        if (null === $this->_pageBlock) {
            $this->_pageBlock = $this->getLayout()
                ->createBlock('elasticsearch/catalogsearch_autocomplete_cms');
        }

        return $this->_pageBlock;
    }

    /**
     * Returns CMS page HTML
     *
     * @param Mage_Cms_Model_Page $page
     * @return string
     */
    public function getPageHtml(Mage_Cms_Model_Page $page)
    {
        return $this->getPageBlock()->setPage($page)->toHtml();
    }

    /**
     * Returns limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->_helper->getAutocompleteLimit();
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
     * Returns reset URL
     *
     * @return string
     */
    public function getResultUrl()
    {
        return $this->helper('catalogsearch')->getResultUrl($this->getQueryText());
    }

    /**
     * Returns entity associated block
     *
     * @param string $entity
     * @return Bubble_Elasticsearch_Block_Catalogsearch_Result
     * @throws Exception
     */
    public function getEntityBlock($entity)
    {
        if (!isset($this->_entityBlocks[$entity])) {
            switch ($entity) {
                case 'product':
                    $this->_entityBlocks[$entity] = $this->getLayout()
                        ->createBlock('elasticsearch/catalogsearch_autocomplete_product');
                    break;
                case 'category':
                    $this->_entityBlocks[$entity] = $this->getLayout()
                        ->createBlock('elasticsearch/catalogsearch_autocomplete_category');
                    break;
                case 'cms':
                    $this->_entityBlocks[$entity] = $this->getLayout()
                        ->createBlock('elasticsearch/catalogsearch_autocomplete_cms');
                    break;
                default:
                    Mage::throwException('Cannot find block for entity ' . $entity);
            }
        }

        return $this->_entityBlocks[$entity];
    }

    /**
     * Returns entity HTML
     *
     * @param string $entity
     * @param Varien_Object $data
     * @return string
     */
    public function getEntityHtml($entity, Varien_Object $data)
    {
        return $this->getEntityBlock($entity)->setEntity($data)->toHtml();
    }

    /**
     * @param string $entity
     * @return string
     */
    public function getEntityTitle($entity)
    {
        return $this->getEntityBlock($entity)->getAutocompleteTitle();
    }

    /**
     * Returns all results label
     *
     * @return string
     */
    public function getAllResultsLabel()
    {
        $label = $this->getLabel('All Results') . ' (%d)';

        return sprintf($label, $this->getProductCollection()->getSize());
    }

    /**
     * @return array
     */
    public function getAllResults()
    {
        $results = array(
            'product'   => $this->getProductCollection(),
            'category'  => $this->getCategoryCollection(),
            'cms'       => $this->getPageCollection(),
        );

        return $results;
    }

    /**
     * @return bool
     */
    public function isNoResult()
    {
        foreach ($this->getAllResults() as $collection) {
            /** @var Varien_Data_Collection $collection */
            if ($collection->count()) {
                return false;
            }
        }

        return true;
    }
}