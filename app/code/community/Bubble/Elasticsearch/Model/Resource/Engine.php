<?php
/**
 * Elasticsearch engine
 *
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Model_Resource_Engine extends Mage_CatalogSearch_Model_Resource_Fulltext_Engine
{
    /**
     * @var Bubble_Elasticsearch_Helper_Data
     */
    protected $_helper;

    /**
     * Cache search results per request
     *
     * @var array
     */
    protected $_cachedData = array();

    /**
     * Initializes search engine
     *
     * @see Bubble_Elasticsearch_Model_Resource_Client
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_helper = Mage::helper('elasticsearch');
    }

    /**
     * @return bool
     */
    public function allowAdvancedIndex()
    {
        return true;
    }

    /**
     * Cleans Elasticsearch index
     *
     * @param int $storeId
     * @param int $ids
     * @param string $type
     * @return Bubble_Elasticsearch_Model_Resource_Engine
     * @throws Exception
     */
    public function cleanIndex($storeId = null, $ids = null, $type = 'product')
    {
        if (null !== $storeId) {
            $stores = array(Mage::app()->getStore($storeId));
        } else {
            $stores = Mage::app()->getStores();
        }

        try {
            foreach ($stores as $store) {
                /** @var $store Mage_Core_Model_Store */
                if (!$store->getIsActive() || !$this->_helper->isActiveEngine($store)) {
                    continue;
                }
                $this->_helper->getClient($store)->cleanStoreIndex($store, $ids, $type);
            }
        } catch (Exception $e) {
            $this->_helper->handleError($e->getMessage());
            throw $e;
        }

        return $this;
    }

    /**
     * Returns advanced search results
     *
     * @return Mage_CatalogSearch_Model_Resource_Advanced_Collection
     */
    public function getAdvancedResultCollection()
    {
        return Mage::getResourceModel('catalogsearch/advanced_collection');
    }

    /**
     * @return array
     */
    public function getAllowedVisibility()
    {
        return Mage::getSingleton('catalog/product_visibility')->getVisibleInSearchIds();
    }

    /**
     * @param null $storeId
     * @param null $ids
     * @param string $type
     * @return Bubble_Elasticsearch_Model_Resource_Engine
     * @throws Exception
     */
    public function rebuildIndex($storeId = null, $ids = null, $type = 'product')
    {
        if (is_array($ids) && empty($ids)) {
            // Do not reindex if ids were passed but were empty
            return $this;
        }

        if (null !== $storeId) {
            $stores = array(Mage::app()->getStore($storeId));
        } else {
            $stores = Mage::app()->getStores();
        }

        try {
            foreach ($stores as $store) {
                /** @var $store Mage_Core_Model_Store */
                $active = $this->_helper->isActiveEngine($store);
                if (!$store->getIsActive() || !$active && $type !== 'product') {
                    continue;
                }
                if (!$active) {
                    // Rebuild product index with Magento default indexer
                    Mage::getSingleton('catalogsearch/fulltext')->rebuildIndex($store->getId(), $ids);
                } else {
                    // Rebuild with Elasticsearch
                    $start = microtime(true);
                    $this->_helper->handleMessage('Start building Elasticsearch index for store %s', $store->getCode());
                    $this->_helper->getClient($store)->saveStoreEntities($store, $ids, $type);
                    $this->_helper->handleMessage('Done in %ss', round(microtime(true) - $start, 2));
                }
            }
        } catch (\Elastica\Exception\ResponseException $e) {
            $error = $e->getMessage();
            $data = $e->getResponse()->getData();
            if (isset($data['error']['reason'])) {
                $error = $data['error']['reason'];
            }
            Mage::throwException('An error occured: ' . $error);
        } catch (Exception $e) {
            $this->_helper->handleError($e->getMessage());
            throw $e;
        }

        return $this;
    }

    /**
     * @param $q
     * @param $store
     * @param $params
     * @param $type
     * @return array|\Elastica\ResultSet
     * @throws Exception
     */
    public function search($q, $store = null, $params = array(), $type = 'product')
    {
        $result = array();

        try {
            $cacheId = sha1(serialize(func_get_args()));
            if (isset($this->_cachedData[$cacheId])) {
                $result = $this->_cachedData[$cacheId];
            } else {
                $store = Mage::app()->getStore($store);
                if ($this->_helper->isActiveEngine($store)) {
                    $result = $this->_helper->getClient($store)->search($q, $store, $params, $type);
                }
                $this->_cachedData[$cacheId] = $result;
            }
        } catch (Exception $e) {
            $this->_helper->handleError($e->getMessage());
        }

        return $result;
    }

    /**
     * Checks Elasticsearch availability.
     *
     * @param mixed $store
     * @return bool
     */
    public function test($store = null)
    {
        return $this->_helper->getClient($store)->test($store);
    }
}
