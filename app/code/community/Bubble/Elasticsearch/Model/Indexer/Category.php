<?php
/**
 * Search indexer override
 *
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Model_Indexer_Category extends Mage_Index_Model_Indexer_Abstract
{
    const EVENT_MATCH_RESULT_KEY = 'catalog_category_match_result';

    /**
     * @var Bubble_Elasticsearch_Helper_Data
     */
    protected $_helper;

    /**
     * @var Bubble_Elasticsearch_Model_Resource_Engine
     */
    protected $_engine;

    /**
     * Initialize indexer
     */
    protected function _construct()
    {
        $this->_helper = Mage::helper('elasticsearch');
        $this->_engine = Mage::helper('catalogsearch')->getEngine();
    }

    /**
     * Indexer must match entities
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Mage_Catalog_Model_Category::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE,
        )
    );

    /**
     * Retrieve indexer name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_helper->__('Elasticsearch Category');
    }

    /**
     * Retrieve indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_helper->__('Rebuild category fulltext search index');
    }

    /**
     * Reindex all category in Elasticsearch
     *
     * @throws Exception
     */
    public function reindexAll()
    {
        foreach (Mage::app()->getStores() as $store) {
            /** @var Mage_Core_Model_Store $store */
            if (!$store->getIsActive() || !$this->_helper->isActiveEngine($store)) {
                continue;
            }
            $this->_engine->rebuildIndex($store, null, 'category');
        }
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        switch ($event->getEntity()) {
            case Mage_Catalog_Model_Category::ENTITY:
                $this->_registerCatalogCategoryEvent($event);
                break;
        }
    }

    /**
     * Get data required for category reindex
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    protected function _registerCatalogCategoryEvent(Mage_Index_Model_Event $event)
    {
        /* @var Mage_Catalog_Model_Category $category */
        $category = $event->getDataObject();
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                $event->addNewData('elasticsearch_update_category', $category->getId());
                break;
            case Mage_Index_Model_Event::TYPE_DELETE:
                $event->addNewData('elasticsearch_delete_category', $category->getId());
                break;
        }

        return $this;
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        if (!empty($data['elasticsearch_update_category'])) {
            $this->_engine->rebuildIndex(null, array($data['elasticsearch_update_category']), 'category');
        }

        if (!empty($data['elasticsearch_delete_category'])) {
            $this->_engine->cleanIndex(null, array($data['elasticsearch_delete_category']), 'category');
        }
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->_helper->isElasticsearchEnabled();
    }
}