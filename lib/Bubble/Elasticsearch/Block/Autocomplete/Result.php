<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Block_Autocomplete_Result extends Bubble_Elasticsearch_Block_Abstract
{
    /**
     * @var string
     */
    protected $_q = '';

    /**
     * @var string
     */
    protected $_template = 'bubble/elasticsearch/autocomplete.phtml';

    /**
     * @var array
     */
    protected $_entityResults = array();

    /**
     * @var array
     */
    protected $_entityResultsCount = array();

    /**
     * @var array
     */
    protected $_entityBlocks = array();

    /**
     * @param string $q
     * @param Bubble_Elasticsearch_Config $config
     */
    public function __construct($q, Bubble_Elasticsearch_Config $config)
    {
        $this->_q = $q;
        $this->_config = $config;
    }

    /**
     * @return string
     */
    public function getAllResultsLabel()
    {
        $label = $this->getLabel('All Results') . ' (%d)';

        return sprintf($label, array_sum($this->_entityResultsCount));
    }

    /**
     * @return string
     */
    public function getResultUrl()
    {
        $baseUrl = $this->_config->getValue('base_url', '');

        return $this->cleanUrl(sprintf('%scatalogsearch/result/?q=%s', $baseUrl, $this->_q));
    }

    /**
     * Returns entity associated block
     *
     * @param string $entity
     * @return Bubble_Elasticsearch_Block_Autocomplete_Abstract
     * @throws Exception
     */
    public function getEntityBlock($entity)
    {
        if (!isset($this->_entityBlocks[$entity])) {
            throw new Exception('Cannot find block for entity ' . $entity);
        }

        return $this->_entityBlocks[$entity];
    }

    /**
     * Associates the entity corresponding block class
     *
     * @param $entity
     * @param Bubble_Elasticsearch_Block_Autocomplete_Abstract $block
     * @return $this
     */
    public function setEntityBlock($entity, Bubble_Elasticsearch_Block_Autocomplete_Abstract $block)
    {
        $this->_entityBlocks[$entity] = $block->setConfig($this->_config);

        return $this;
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
        return $this->getEntityBlock($entity)->getTitle();
    }

    /**
     * @param string $entity
     * @return array
     */
    public function getEntityResults($entity)
    {
        return isset($this->_entityResults[$entity]) ? $this->_entityResults[$entity] : array();
    }

    /**
     * @return array
     */
    public function getAllResults()
    {
        return $this->_entityResults;
    }

    /**
     * @param string $entity
     * @param array $results
     * @return $this
     */
    public function setEntityResults($entity, array $results)
    {
        if (!empty($results)) {
            $this->_entityResults[$entity] = $results;
        }

        return $this;
    }

    /**
     * @param string $entity
     * @return array
     */
    public function getEntityResultsCount($entity)
    {
        return isset($this->_entityResultsCount[$entity]) ? $this->_entityResultsCount[$entity] : 0;
    }

    /**
     * @param string $entity
     * @param int $count
     * @return $this
     */
    public function setEntityResultsCount($entity, $count)
    {
        $this->_entityResultsCount[$entity] = (int) $count;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNoResult()
    {
        return empty($this->_entityResults);
    }
}