<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
/**
 * @method  Mage_Catalog_Model_Category getEntity()
 * @method  $this                       setEntity(Mage_Catalog_Model_Category $category)
 */
class Bubble_Elasticsearch_Block_Catalogsearch_Autocomplete_Category
    extends Bubble_Elasticsearch_Block_Catalogsearch_Result
{
    /**
     * @var string
     */
    protected $_autocompleteTitle = 'Categories';

    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('bubble/elasticsearch/autocomplete/category.phtml');
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryUrl(Mage_Catalog_Model_Category $category)
    {
        return $category->getUrl();
    }
}