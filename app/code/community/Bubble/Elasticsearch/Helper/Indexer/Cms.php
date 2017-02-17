<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Helper_Indexer_Cms extends Bubble_Elasticsearch_Helper_Indexer_Abstract
{
    /**
     * @var string
     */
    protected $_blockClass = 'Bubble_Elasticsearch_Block_Autocomplete_Cms';

    /**
     * Export CMS pages according to optional filters
     *
     * @param array $filters
     * @return array
     */
    public function export($filters = array())
    {
        $result = array();

        foreach (Mage::app()->getStores() as $store) {
            /** @var $store Mage_Core_Model_Store */
            if (!$store->getIsActive()) {
                continue;
            }

            $storeId = (int) $store->getId();

            if (isset($filters['store_id'])) {
                if (!is_array($filters['store_id'])) {
                    $filters['store_id'] = array($filters['store_id']);
                }
                if (!in_array($storeId, $filters['store_id'])) {
                    continue;
                }
            }

            $this->handleMessage(' > Exporting CMS pages of store %s', $store->getCode());

            $result[$storeId] = array();

            /** @var Mage_Cms_Model_Resource_Page_Collection $collection */
            $attributesConfig = $this->getSearchableAttributesConfig('cms', $store);
            $collection = Mage::getModel('cms/page')->getCollection()
                ->addStoreFilter($store->getId())
                ->addFieldToSelect($attributesConfig);

            if ($excluded = $this->getExcludedPageIds($store)) {
                $collection->addFieldToFilter('page_id', array('nin' => $excluded));
            }

            foreach ($collection as $page) {
                $result[$storeId][$page->getId()] = array_merge(
                    array('id' => $page->getId()),
                    $page->toArray($attributesConfig)
                );
            }

            $this->handleMessage(' > CMS pages exported');
        }

        return $result;
    }

    /**
     * @param mixed $store
     * @return array
     */
    public function getExcludedPageIds($store = null)
    {
        return explode(',', Mage::getStoreConfig('elasticsearch/cms/excluded_pages', $store));
    }

    /**
     * Builds store index properties for indexation
     *
     * @param mixed $store
     * @return array
     */
    public function getStoreIndexProperties($store = null)
    {
        $store = Mage::app()->getStore($store);
        $cacheId = 'elasticsearch_cms_index_properties_' . $store->getId();
        if (Mage::app()->useCache('config')) {
            $properties = Mage::app()->loadCache($cacheId);
            if ($properties) {
                return unserialize($properties);
            }
        }

        $indexSettings = $this->getStoreIndexSettings($store);
        $properties = array();
        $resource = Mage::getResourceModel('cms/page');
        $tableInfo = $resource->getReadConnection()->describeTable($resource->getMainTable());

        foreach ($this->getSearchableAttributesConfig('cms', $store) as $field) {
            if (isset($tableInfo[$field])) {
                $properties[$field] = array(
                    'type' => 'string',
                    'analyzer' => 'std',
                    'include_in_all' => true,
                    'boost' => 1, // boost at query time
                    'fields' => array(
                        'std' => array(
                            'type' => 'string',
                            'analyzer' => 'std',
                        )
                    ),
                );
                if ($tableInfo[$field]['DATA_TYPE'] == Varien_Db_Ddl_Table::TYPE_VARCHAR) {
                    $properties[$field]['fields'] = array_merge($properties[$field]['fields'], array(
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
                    $properties[$field]['analyzer'] = 'language';
                }
            }
        }

        $properties = new Varien_Object($properties);

        Mage::dispatchEvent('bubble_elasticsearch_index_properties', array(
            'indexer' => $this,
            'store' => $store,
            'properties' => $properties,
        ));

        $properties = $properties->getData();

        if (Mage::app()->useCache('config')) {
            $lifetime = $this->getCacheLifetime();
            Mage::app()->saveCache(serialize($properties), $cacheId, array('config'), $lifetime);
        }

        return $properties;
    }
}