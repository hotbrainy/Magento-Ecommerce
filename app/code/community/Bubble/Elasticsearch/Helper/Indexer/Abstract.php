<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
abstract class Bubble_Elasticsearch_Helper_Indexer_Abstract extends Bubble_Elasticsearch_Helper_Data
{
    /**
     * @var string Date format
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/mapping-date-format.html
     */
    protected $_dateFormat = 'date';

    /**
     * @var string
     */
    protected $_blockClass = '';

    /**
     * Export data according to optional filters
     *
     * @param array $filters
     * @return array
     */
    abstract public function export($filters = array());

    /**
     * Builds store index properties
     *
     * @param mixed $store
     * @return mixed
     */
    abstract public function getStoreIndexProperties($store = null);

    /**
     * Returns potential additional fields to add to Elasticsearch query
     *
     * @return array
     */
    public function getAdditionalFields()
    {
        return array();
    }

    /**
     * @return string
     */
    public function getBlockClass()
    {
        return $this->_blockClass;
    }

    /**
     * Returns attribute properties for indexation
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @param mixed $store
     * @return array
     */
    public function getAttributeProperties(Mage_Eav_Model_Entity_Attribute $attribute, $store = null)
    {
        $indexSettings = $this->getStoreIndexSettings($store);
        $type = $this->getAttributeType($attribute);
        $weight = $attribute->getSearchWeight();

        if ($type === 'option') {
            // Define field for option label
            $properties = array(
                'type' => 'string',
                'analyzer' => 'std',
                'index_options' => 'docs', // do not use tf/idf for options
                'norms' => array('enabled' => false), // useless for options
                'include_in_all' => (bool) $attribute->getIsSearchable(),
                'boost' => $weight > 0 ? intval($weight) : 1, // boost at query time
                'fields' => array(
                    'std' => array(
                        'type' => 'string',
                        'analyzer' => 'std',
                        'index_options' => 'docs', // do not use tf/idf for options
                        'norms' => array('enabled' => false), // useless for options
                    ),
                ),
            );
            if (isset($indexSettings['analysis']['analyzer']['language'])) {
                $properties['analyzer'] = 'language';
            }
        } elseif ($type === 'integer') {
            $properties = array(
                'type' => $type,
                'ignore_malformed' => true,
                'index' => 'not_analyzed',
            );
        } elseif ($type !== 'string') {
            $properties = array(
                'type' => $type,
                'analyzer' => 'std',
                'include_in_all' => (bool) $attribute->getIsSearchable(),
                'search_analyzer' => 'std',
            );
        } else {
            $properties = array(
                'type' => 'string',
                'analyzer' => 'std',
                'include_in_all' => (bool) $attribute->getIsSearchable(),
                'boost' => $weight > 0 ? intval($weight) : 1, // boost at query time
                'fields' => array(
                    'std' => array(
                        'type' => 'string',
                        'analyzer' => 'std',
                    ),
                ),
            );
            if ($attribute->getBackendType() != 'text') {
                $properties['fields'] = array_merge($properties['fields'], array(
                    'prefix' => array(
                        'type' => 'string',
                        'analyzer' => 'text_prefix',
                        'search_analyzer' => 'std',
                    ),
                    'suffix' => array(
                        'type' => 'string',
                        'analyzer' => 'text_suffix',
                        'search_analyzer' => 'std',
                    ),
                ));
            }
            if (isset($indexSettings['analysis']['analyzer']['language'])) {
                $properties['analyzer'] = 'language';
            }
        }

        return $properties;
    }

    /**
     * Returns attribute type for indexation
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return string
     */
    public function getAttributeType(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        $type = 'string';
        if ($attribute->getBackendType() == 'decimal' || $attribute->getFrontendClass() == 'validate-number') {
            $type = 'double';
        } elseif ($attribute->getSourceModel() == 'eav/entity_attribute_source_boolean') {
            $type = 'boolean';
        } elseif ($attribute->getBackendType() == 'datetime') {
            $type = 'date';
        } elseif ($this->isAttributeUsingOptions($attribute)) {
            $type = 'option'; // custom type
        } elseif ($attribute->usesSource() && $attribute->getBackendType() == 'int'
            || $attribute->getFrontendClass() == 'validate-digits')
        {
            $type = 'integer';
        }

        return $type;
    }
}