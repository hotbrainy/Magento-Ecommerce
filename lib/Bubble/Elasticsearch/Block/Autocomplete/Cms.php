<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Block_Autocomplete_Cms extends Bubble_Elasticsearch_Block_Autocomplete_Abstract
{
    /**
     * @var string
     */
    protected $_title = 'Pages';

    /**
     * @var string
     */
    protected $_template = 'bubble/elasticsearch/autocomplete/cms.phtml';

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_config->getValue('base_url', '');
    }
}