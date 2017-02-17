<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Client extends \Elastica\Client
{
    /**
     * @param string $name
     * @return Bubble_Elasticsearch_Index
     */
    public function getIndex($name)
    {
        return new Bubble_Elasticsearch_Index($this, $name);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getIndexAlias($name)
    {
        $prefix = $this->getConfig('index_prefix');

        return $prefix . $name;
    }

    /**
     * @param string $name
     * @param bool $new
     * @return string
     */
    public function getIndexName($name, $new = false)
    {
        $alias = $this->getIndexAlias($name);
        $name = $alias . '_idx1'; // index name must be different than alias name
        foreach ($this->getStatus()->getIndicesWithAlias($alias) as $indice) {
            if ($new) {
                $name = $indice->getName() != $name ? $name : $alias . '_idx2';
            } else {
                $name = $indice->getName();
            }
        }

        return $name;
    }

    /**
     * Returns query operator
     *
     * @return string
     */
    public function getQueryOperator()
    {
        return $this->getConfig('query_operator');
    }

    /**
     * Checks if fuzzy query is enabled
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-flt-query.html
     * @return bool
     */
    public function isFuzzyQueryEnabled()
    {
        return (bool) $this->getConfig('enable_fuzzy_query');
    }

    /**
     * Prepares query text for search
     *
     * @param string $text
     * @return string
     */
    public function prepareQueryText($text)
    {
        $words = explode(' ', $text);
        $words = array_filter($words, 'strlen');
        $text = implode(' ', $words);

        return $text;
    }

    /**
     * @param Bubble_Elasticsearch_Type $type
     * @param string $q
     * @param array $params
     * @return \Elastica\Search
     */
    public function getSearch(Bubble_Elasticsearch_Type $type, $q, $params = array())
    {
        if (empty($params)) {
            $params = array('limit' => 10000); // should be enough
        }

        $q = $this->prepareQueryText($q);

        $bool = new \Elastica\Query\BoolQuery();

        /**
         * Using cross-fields because it seems the best approach for entity search (products, categories, ...).
         * Cross-fields multi-match query has to work on fields that have the same analyzer.
         * So we build such a query for each configured analyzers.
         *
         * @link https://www.elastic.co/guide/en/elasticsearch/guide/current/_cross_fields_entity_search.html
         */
        foreach ($type->getAnalyzers() as $analyzer) {
            $fields = $type->getSearchFields($q, $analyzer);
            if (!empty($fields)) {
                $query = new \Elastica\Query\MultiMatch();
                $query->setQuery($q);
                $query->setType('cross_fields');
                $query->setFields($fields);
                $query->setOperator($this->getQueryOperator());
                $query->setTieBreaker(.1);
                $bool->addShould($query);
            }
        }

        if ($this->isFuzzyQueryEnabled()) {
            $fields = $type->getSearchFields($q, 'std');
            if (!empty($fields)) {
                $query = new \Elastica\Query\Match('_all', array(
                    'query' => $q,
                    'operator' => 'AND',
                    'fuzziness' => 'AUTO',
                ));
                $bool->addShould($query);
            }
        }

        $search = $type->createSearch($bool, $params);

        $additionalFields = $type->getAdditionalFields();
        if (!empty($additionalFields)) {
            // Return additional fields (entity id is already implicitly included)
            $search->getQuery()->setFields($additionalFields);
        }

        return $search;
    }
}