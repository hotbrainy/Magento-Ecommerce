<?php
/**
 * Fulltext collection override
 *
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Model_Resource_Fulltext_Collection
    extends Mage_CatalogSearch_Model_Resource_Fulltext_Collection
{
    /**
     * @var bool
     */
    protected $_active = false;

    /**
     * @var ArrayObject Store results per session to avoid multiple useless requests for a same query
     */
    protected $_results;

    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_active = Mage::helper('elasticsearch')->isActiveEngine($this->getStoreId());
        $session = Mage::getSingleton('core/session');
        $key = 'elasticsearch_results_' . $this->getStoreId();
        if (!$session->hasData($key)) {
            $session->setData($key, new ArrayObject());
        }
        $this->_results = $session->getData($key);
    }

    /**
     * @param $query
     * @return array
     */
    protected function _getResults($query)
    {
        if ($this->_results->offsetExists($query)) {
            return $this->_results->offsetGet($query);
        }

        return array();
    }

    /**
     * @param $query
     * @param $ids
     * @return $this
     */
    protected function _setResults($query, $ids)
    {
        $this->_results->offsetSet($query, (array) $ids);

        return $this;
    }

    /**
     * @param string $query
     * @return Bubble_Elasticsearch_Model_Resource_Fulltext_Collection
     */
    public function addSearchFilter($query)
    {
        if (!$this->_active) {
            return parent::addSearchFilter($query);
        }

        $ids = $this->_getResults($query);
        if (empty($ids)) {
            $queryObject = Mage::helper('catalogsearch')->getQuery();
            if ($queryObject->getSynonymFor()) {
                $query = $queryObject->getSynonymFor();
            }
            /** @var Bubble_Elasticsearch_Model_Resource_Engine $engine */
            $engine = Mage::helper('catalogsearch')->getEngine();
            $search = $engine->search($query);

            if ($search instanceof \Elastica\ResultSet) {
                $ids = array();
                foreach ($search->getResults() as $result) {
                    /** @var \Elastica\Result $result */
                    $ids[] = (int)$result->getId();
                    if (isset($result->_parent_ids)) {
                        $ids = array_merge($ids, $result->_parent_ids);
                    }
                }
                $ids = array_values(array_unique($ids));

                $suggests = array();
                foreach ($search->getSuggests() as $suggestions) {
                    foreach ($suggestions as $suggestion) {
                        if (isset($suggestion['options']) && !empty($suggestion['options'])) {
                            foreach ($suggestion['options'] as $phrase) {
                                $text = $phrase['text'];
                                $score = $phrase['score'];
                                if ($score < .01) {
                                    continue;
                                }
                                if (!isset($suggests[$text])) {
                                    $suggests[$text] = 0;
                                }
                                if ($score > $suggests[$text]) {
                                    $suggests[$text] = $score;
                                }
                            }
                        }
                    }
                }
                if (!empty($suggests)) {
                    arsort($suggests); // retrieve the best score
                    $this->setFlag('suggest', key($suggests));
                }
            }
        }

        $this->_setResults($query, $ids);
        $this->setFlag('ids', $ids);

        if (empty($ids)) {
            $this->addIdFilter(array(0)); // Workaround for no result
        } else {
            $this->addIdFilter($ids);
        }

        // Show extension version for debug purpose, can be disabled in config
        Mage::helper('elasticsearch')->addResponseHeader();

        return $this;
    }

    /**
     * @param string $attribute
     * @param string $dir
     * @return Bubble_Elasticsearch_Model_Resource_Fulltext_Collection
     */
    public function setOrder($attribute, $dir = 'desc')
    {
        if ($this->_active && $attribute == 'relevance') {
            $ids = $this->getFlag('ids');
            if (!empty($ids)) {
                if ($dir == 'asc') {
                    $ids = array_reverse($ids);
                }
                $this->getSelect()
                    ->order(new Zend_Db_Expr('FIELD(e.entity_id, ' . implode(', ', $ids) . ')'));
            }
        } else {
            parent::setOrder($attribute, $dir);
        }

        return $this;
    }
}
