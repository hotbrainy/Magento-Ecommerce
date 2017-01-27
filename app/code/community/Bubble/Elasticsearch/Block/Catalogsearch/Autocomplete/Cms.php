<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
/**
 * @method  Mage_Cms_Model_Page getEntity()
 * @method  $this               setEntity(Mage_Cms_Model_Page $page)
 */
class Bubble_Elasticsearch_Block_Catalogsearch_Autocomplete_Cms
    extends Bubble_Elasticsearch_Block_Catalogsearch_Result
{
    /**
     * @var string
     */
    protected $_autocompleteTitle = 'Pages';

    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('bubble/elasticsearch/autocomplete/cms.phtml');
    }
}