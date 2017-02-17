<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Index extends \Elastica\Index
{
    /**
     * @var array
     */
    protected $_analyzers = array();

    /**
     * @return array
     */
    public function getAnalyzers()
    {
        return $this->_analyzers;
    }

    /**
     * @param array $analyzers
     * @return $this
     */
    public function setAnalyzers(array $analyzers)
    {
        $this->_analyzers = $analyzers;

        return $this;
    }
}