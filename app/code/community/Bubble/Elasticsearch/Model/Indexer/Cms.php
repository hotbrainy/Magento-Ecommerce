<?php
/**
 * Search indexer override
 *
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Model_Indexer_Cms extends Mage_Index_Model_Indexer_Abstract
{
    const EVENT_MATCH_RESULT_KEY = 'cms_match_result';

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
        'cms_page' => array(
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
        return $this->_helper->__('Elasticsearch CMS Pages');
    }

    /**
     * Retrieve indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->_helper->__('Rebuild CMS pages fulltext search index');
    }

    /**
     * Reindex all CMS pages in Elasticsearch
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
            $this->_engine->rebuildIndex($store, null, 'cms');
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
            case 'cms_page':
                $this->_registerCatalogCmsEvent($event);
                break;
        }
    }

    /**
     * Get data required for CMS page reindex
     *
     * @param Mage_Index_Model_Event $event
     * @return $this
     */
    protected function _registerCatalogCmsEvent(Mage_Index_Model_Event $event)
    {
        /* @var Mage_Cms_Model_Page $page */
        $page = $event->getDataObject();
        switch ($event->getType()) {
            case Mage_Index_Model_Event::TYPE_SAVE:
                $event->addNewData('elasticsearch_update_page', $page->getId());
                break;
            case Mage_Index_Model_Event::TYPE_DELETE:
                $event->addNewData('elasticsearch_delete_page', $page->getId());
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

        if (!empty($data['elasticsearch_update_page'])) {
            $this->_engine->rebuildIndex(null, array($data['elasticsearch_update_page']), 'cms');
        }

        if (!empty($data['elasticsearch_delete_page'])) {
            $this->_engine->cleanIndex(null, array($data['elasticsearch_delete_page']), 'cms');
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