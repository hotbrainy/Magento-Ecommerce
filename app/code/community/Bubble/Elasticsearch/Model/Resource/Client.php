<?php
/**
 * Elasticsearch client
 *
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */

spl_autoload_register(array('Bubble_Elasticsearch_Model_Autoload', 'load'), false, true);

class Bubble_Elasticsearch_Model_Resource_Client extends Bubble_Elasticsearch_Client
{
    /**
     * @var Bubble_Elasticsearch_Helper_Data
     */
    protected $_helper;

    /**
     * @var array Saves search engine availability per store
     */
    protected $_test = array();

    /**
     * Initializes search engine configuration
     *
     * @param array $config
     * @param null $callback
     */
    public function __construct(array $config = array(), $callback = null)
    {
        $this->_helper = Mage::helper('elasticsearch');
        parent::__construct($config, $callback);
    }

    /**
     * Cleans store index
     *
     * @param mixed $store
     * @param int $ids
     * @param string $type
     * @return Bubble_Elasticsearch_Model_Resource_Client
     */
    public function cleanStoreIndex($store = null, $ids = null, $type = 'product')
    {
        $index = $this->getStoreIndexName($store);
        if ($this->indexExists($index)) {
            $type = $this->getIndex($index)->getType($type);
            if ($type->exists()) {
                if (empty($ids)) {
                    // Delete ALL docs from specific index
                    $type->request('', \Elastica\Request::DELETE);
                } else {
                    // Delete matching ids from specific index
                    $type->deleteIds((array) $ids);
                }
            }
        }

        return $this;
    }

    /**
     * Creates document in a clean format
     *
     * @param string $index
     * @param string $id
     * @param array $data
     * @param string $type
     * @return \Elastica\Document
     */
    public function createDoc($index, $id = '', array $data = array(), $type = 'product')
    {
        return new \Elastica\Document($id, $data, $type, $index);
    }

    /**
     * Returns indexer of specified type
     *
     * @param string $type
     * @return Bubble_Elasticsearch_Helper_Indexer_Abstract
     */
    public function getIndexer($type = 'product')
    {
        return Mage::helper('elasticsearch/indexer_' . $type);
    }

    /**
     * @param mixed $store
     * @return Mage_Core_Model_Store
     */
    public function getStore($store = null)
    {
        return Mage::app()->getStore($store);
    }

    /**
     * @param $store
     * @param null $ids
     * @param string $type
     * @return mixed
     */
    public function getStoreData($store = null, $ids = null, $type = 'product')
    {
        $store = $this->getStore($store);
        $filters = array('store_id' => $store->getId());
        if (!empty($ids)) {
            $filters['entity_id'] = array_unique($ids);
        }
        $data = $this->getIndexer($type)->export($filters);

        Mage::dispatchEvent('bubble_elasticsearch_store_export_data', array(
            'type'  => $type,
            'store' => $store,
            'data'  => &$data,
        ));

        return $data[$store->getId()];
    }

    /**
     * @param mixed $store
     * @param bool $new
     * @return Bubble_Elasticsearch_Index
     * @throws Exception
     */
    public function getStoreIndex($store = null, $new = false)
    {
        if (!$this->isSafeReindexEnabled()) {
            $new = false; // reindex on current index and not on a temporary one
        }

        $name = $this->getStoreIndexName($store, $new);
        $index = $this->getIndex($name);

        // Delete index if exists because we are indexing all documents
        if ($new && $this->indexExists($name)) {
            $index->delete();
        }

        // If index doesn't exist, create it with store settings
        if (!$this->indexExists($name)) {
            $index->create(array('settings' => $this->_helper->getStoreIndexSettings($store)));
            if (!$new) {
                $index->addAlias($this->getStoreIndexAlias($store), true);
            }

            Mage::helper('elasticsearch/autocomplete')->saveConfig();
        }

        // Send index mapping if not yet defined
        foreach ($this->_helper->getStoreTypes($store) as $type) {
            if ($index->getType($type)->exists()) {
                continue;
            }
            $mapping = new \Elastica\Type\Mapping();
            $mapping->setType($index->getType($type));
            if (!$this->isSourceEnabled($store)) {
                $mapping->disableSource();
            }

            // Hanle boost at query time
            $properties = $this->getIndexer($type)->getStoreIndexProperties($store);
            foreach ($properties as &$field) {
                if (isset($field['boost'])) {
                    unset($field['boost']);
                }
            }
            unset($field);

            $mapping->setAllField(array('analyzer' => 'std'));

            $mapping->setProperties($properties);

            Mage::dispatchEvent('bubble_elasticsearch_mapping_send_before', array(
                'client' => $this,
                'store' => $store,
                'mapping' => $mapping,
                'type' => $type,
            ));

            $mapping->getType()->request(
                '_mapping',
                \Elastica\Request::PUT,
                $mapping->toArray(),
                array('update_all_types' => true)
            );
        }

        // Set index analyzers for future search
        $index->setAnalyzers($this->_helper->getStoreAnalyzers($store));

        return $index;
    }

    /**
     * @param mixed $store
     * @return string
     */
    public function getStoreIndexAlias($store)
    {
        return $this->getIndexAlias($this->getStore($store)->getCode());
    }

    /**
     * @param mixed $store
     * @param bool $new
     * @return string
     */
    public function getStoreIndexName($store, $new = false)
    {
        return $this->getIndexName($this->getStore($store)->getCode(), $new);
    }

    /**
     * @param $store
     * @param string $type
     * @return Bubble_Elasticsearch_Type
     */
    public function getStoreType($store, $type = 'product')
    {
        return new Bubble_Elasticsearch_Type($this->getStoreIndex($store), $type);
    }

    /**
     * Retrieve suggest fields
     *
     * @return array
     */
    public function getSuggestFields()
    {
        $fields = new Varien_Object(array('name.std'));

        Mage::dispatchEvent('bubble_elasticsearch_suggest_fields', array(
            'client' => $this,
            'fields' => $fields,
        ));

        return $fields->getData();
    }

    /**
     * Checks if given index already exists
     * Here because of a bug when calling exists() method directly on index object during index process
     *
     * @param mixed $index
     * @return bool
     */
    public function indexExists($index)
    {
        return $this->getStatus()->indexExists($index);
    }

    /**
     * Checks if we reindex in a temporary index or not
     *
     * @return bool
     */
    public function isSafeReindexEnabled()
    {
        return (bool) $this->getConfig('safe_reindex');
    }

    /**
     * Checks if data should be either stored or not
     *
     * @param mixed $store
     * @return bool
     */
    public function isSourceEnabled($store = null)
    {
        return (bool) $this->getConfig('enable_source')
            || Mage::helper('elasticsearch/autocomplete')->isFastAutocompleteEnabled($store);
    }

    /**
     * Checks if suggestion is enabled
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters-phrase.html
     * @return bool
     */
    public function isSuggestEnabled()
    {
        return (bool) $this->getConfig('enable_suggest');
    }

    /**
     * Saves entities to specified index
     *
     * @param $index
     * @param $data
     * @param string $type
     * @return $this
     */
    public function saveEntities($index, $data, $type = 'product')
    {
        if (is_string($index)) {
            $index = $this->getIndex($index);
        }
        $chunks = array_chunk($data, 100);
        foreach ($chunks as $docs) {
            foreach ($docs as $k => $doc) {
                $docs[$k] = $this->createDoc($index, $doc['id'], $doc, $type);
            }
            try {
                $this->addDocuments($docs);
            } catch (Exception $e) {
                $this->_helper->handleError($e->getMessage());
                $this->_helper->handleError(print_r($docs, true));
            }
        }
        $index->refresh();

        return $this;
    }

    /**
     * Saves store entities
     *
     * @param mixed $store
     * @param array $ids
     * @param string $type
     * @return Bubble_Elasticsearch_Model_Resource_Client
     */
    public function saveStoreEntities($store = null, $ids = null, $type = 'product')
    {
        $data = $this->getStoreData($store, $ids, $type);
        $new = empty($ids) && $type == 'product'; // create a new index if full product reindexation
        if (empty($ids) && $type != 'product') {
            $this->cleanStoreIndex($store, null, $type);
        }
        $index = $this->getStoreIndex($store, $new);
        $this->saveEntities($index, $data, $type);

        if ($new) {
            $this->switchStoreIndex($index, $store);
        }

        return $this;
    }

    /**
     * @param string $q
     * @param mixed $store
     * @param array $params
     * @param string $type
     * @return array|\Elastica\ResultSet
     */
    public function search($q, $store = null, $params = array(), $type = 'product')
    {
        $indexer = $this->getIndexer($type);

        $type = $this->getStoreType($store, $type);
        $type->setIndexProperties($indexer->getStoreIndexProperties($store));
        $type->setAdditionalFields($indexer->getAdditionalFields());

        $search = $this->getSearch($type, $q, $params);

        if ($this->isSuggestEnabled()) {
            $suggest = new \Elastica\Suggest();
            $fields = $type->getSearchFields($q, 'std', false);
            foreach ($this->getSuggestFields() as $field) {
                if (in_array($field, $fields)) {
                    $suggestField = new \Elastica\Suggest\Phrase($field, $field);
                    $suggestField->setText($q);
                    $suggestField->setGramSize(1);
                    $suggestField->setMaxErrors(.9);
                    $candidate = new \Elastica\Suggest\CandidateGenerator\DirectGenerator($field);
                    $candidate->setParam('min_word_length', 3);
                    $suggestField->addCandidateGenerator($candidate);
                    $suggest->addSuggestion($suggestField);
                    $search->getQuery()->setSuggest($suggest);
                }
            }
        }

        Mage::dispatchEvent('bubble_elasticsearch_before_search', array(
            'client' => $this,
            'search' => $search,
            'store' => $store,
            'type' => $type,
        ));

        Varien_Profiler::start('ELASTICA_SEARCH');

        $result = $search->search();

        Varien_Profiler::stop('ELASTICA_SEARCH');

        return $result;
    }

    /**
     * Switch index of specified store by linking alias on it
     *
     * @param mixed $index
     * @param mixed $store
     * @return Bubble_Elasticsearch_Model_Resource_Client
     */
    public function switchStoreIndex($index, $store = null)
    {
        if (is_string($index)) {
            $index = $this->getIndex($index);
        }
        $alias = $this->getStoreIndexAlias($store);
        foreach ($this->getStatus()->getIndicesWithAlias($alias) as $indice) {
            if ($indice->getName() != $index->getName()) {
                $indice->delete(); // remove old indice that was linked to the alias
            }
        }
        $index->addAlias($alias, true);

        return $this;
    }

    /**
     * Test if Elasticsearch server is reachable for given store
     *
     * @param mixed $store
     * @return bool
     */
    public function test($store = null)
    {
        $store = Mage::app()->getStore($store);
        if (!isset($this->_test[$store->getId()])) {
            try {
                $this->getStatus();
                $this->_test[$store->getId()] = true;
            } catch (Exception $e) {
                $this->_test[$store->getId()] = false;
                $this->_helper->handleError('Elasticsearch server is not reachable');
            }
        }

        return $this->_test[$store->getId()];
    }
}