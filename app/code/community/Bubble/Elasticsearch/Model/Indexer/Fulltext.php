<?php
/**
 * Search indexer override
 *
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Model_Indexer_Fulltext extends Mage_CatalogSearch_Model_Indexer_Fulltext
{
    /**
     * @var Bubble_Elasticsearch_Helper_Data
     */
    protected $_helper;

    /**
     * @var Bubble_Elasticsearch_Model_Resource_Engine
     */
    protected $_engine;

    /**
     * Indexer initialization
     */
    protected function _construct()
    {
        $this->_helper = Mage::helper('elasticsearch');
        $this->_engine = Mage::helper('catalogsearch')->getEngine();
    }

    /**
     * @param mixed $store
     * @return bool
     */
    public function isActiveEngine($store = null)
    {
        return $this->_helper->isActiveEngine($store);
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->_helper->isElasticsearchEnabled()) {
            return $this->_helper->__('Elasticsearch Product');
        }

        return parent::getName();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        if ($this->_helper->isElasticsearchEnabled()) {
            return $this->_helper->__('Rebuild product fulltext search index');
        }

        return parent::getDescription();
    }

    /**
     * Rebuild all index data
     *
     * @return void
     */
    public function reindexAll()
    {
        foreach (Mage::app()->getStores() as $store) {
            /** @var Mage_Core_Model_Store $store */
            if (!$store->getIsActive()) {
                continue;
            }
            if (!$this->isActiveEngine($store)) {
                $this->_getIndexer()->rebuildIndex($store->getId());
            } else {
                try {
                    // Need to reindex all types since we have maybe switched to a new index (safe reindex mode)
                    foreach ($this->_helper->getStoreTypes($store) as $type) {
                        $this->_engine->rebuildIndex($store, null, $type);
                    }
                } catch (\Elastica\Exception\ResponseException $e) {
                    $this->_helper->handleError($e->getMessage());
                    throw $e;
                }
            }
        }
    }

    /**
     * @param Mage_Index_Model_Event $event
     * @throws Exception
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['catalogsearch_fulltext_reindex_all'])) {
            $this->reindexAll();
        } else {
            if (!empty($data['catalogsearch_delete_product_id'])) {
                $productId = $data['catalogsearch_delete_product_id'];

                $this->_engine->cleanIndex(null, array($productId));
            } else if (!empty($data['catalogsearch_update_product_id'])) {
                $productId = $data['catalogsearch_update_product_id'];

                $this->_engine->rebuildIndex(null, array($productId));
            } else if (!empty($data['catalogsearch_product_ids'])) {
                // Mass action
                $productIds = $data['catalogsearch_product_ids'];

                $this->_engine->rebuildIndex(null, $productIds);
            } else if (isset($data['catalogsearch_category_update_product_ids'])) {
                $productIds = $data['catalogsearch_category_update_product_ids'];
                $categoryIds = $data['catalogsearch_category_update_category_ids'];

                $this->_getIndexer()->updateCategoryIndex($productIds, $categoryIds);
            }
        }
    }
}