<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Block_Autocomplete_Category extends Bubble_Elasticsearch_Block_Autocomplete_Abstract
{
    /**
     * @var string
     */
    protected $_title = 'Categories';

    /**
     * @var string
     */
    protected $_template = 'bubble/elasticsearch/autocomplete/category.phtml';

    /**
     * @param Varien_Object $category
     * @return string
     */
    public function getCategoryPathName(Varien_Object $category)
    {
        if ($this->_config->getConfig('category/show_path', true)) {
            return $category->getData('_path');
        }

        return $category->getName();
    }

    /**
     * @param Varien_Object $category
     * @return string
     */
    public function getCategoryUrl(Varien_Object $category)
    {
        return $this->cleanUrl($category->getData('_url'));
    }
}