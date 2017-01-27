<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Helper_Indexer_Category extends Bubble_Elasticsearch_Helper_Indexer_Abstract
{
    /**
     * Searchable attributes
     *
     * @var array
     */
    protected $_searchableAttributes;

    /**
     * @var Mage_Catalog_Model_Resource_Category_Collection[]
     */
    protected $_categoriesWithPathNames = array();

    /**
     * @var string
     */
    protected $_blockClass = 'Bubble_Elasticsearch_Block_Autocomplete_Category';

    /**
     * Export categories according to optional filters
     *
     * @param array $filters
     * @return array
     */
    public function export($filters = array())
    {
        $result = array();
        $currentStore = Mage::app()->getStore();

        foreach (Mage::app()->getStores() as $store) {
            /** @var $store Mage_Core_Model_Store */
            if (!$store->getIsActive()) {
                continue;
            }

            Mage::app()->setCurrentStore($store);

            $storeId = (int) $store->getId();

            if (isset($filters['store_id'])) {
                if (!is_array($filters['store_id'])) {
                    $filters['store_id'] = array($filters['store_id']);
                }
                if (!in_array($storeId, $filters['store_id'])) {
                    continue;
                }
            }

            $this->handleMessage(' > Exporting categories of store %s', $store->getCode());

            $result[$storeId] = array();

            /** @var Mage_Catalog_Model_Resource_Category_Collection $collection */
            $attributesConfig = $this->getSearchableAttributesConfig('category', $store);
            $collection = Mage::getModel('catalog/category')->getCollection();
            $collection->setStoreId($store->getId());
            $rootCategoryId = $store->getRootCategoryId();
            $collection->addIsActiveFilter()
                ->addAttributeToSelect($attributesConfig)
                ->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"));

            $collection->addUrlRewriteToResult();

            foreach ($collection as $category) {
                $category->getUrlInstance()->setStore($store);
                $result[$storeId][$category->getId()] = array_merge(
                    array(
                        'id' => $category->getId(),
                        '_url' => $category->getUrl(),
                        '_path' => $this->getCategoryPathName($category),
                    ),
                    $category->toArray($attributesConfig)
                );
            }

            $this->handleMessage(' > Categories exported');
        }

        Mage::app()->setCurrentStore($currentStore);

        return $result;
    }

    /**
     * Retrieves all searchable category attributes
     * Possibility to filter attributes by backend type
     *
     * @param mixed $store
     * @return array
     */
    public function getSearchableAttributes($store = null)
    {
        if (null === $this->_searchableAttributes) {
            $this->_searchableAttributes = array();

            $entityType = $this->getEavConfig()->getEntityType('catalog_category');
            $entity = $entityType->getEntity();

            /* @var Mage_Catalog_Model_Resource_Category_Attribute_Collection $categoryAttributeCollection */
            $categoryAttributeCollection = Mage::getResourceModel('catalog/category_attribute_collection')
                ->setEntityTypeFilter($entityType->getEntityTypeId())
                ->addFieldToFilter('attribute_code', array(
                    'in' => $this->getSearchableAttributesConfig('category', $store)
                ));

            $attributes = $categoryAttributeCollection->getItems();
            foreach ($attributes as $attribute) {
                /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
                $attribute->setEntity($entity);
                $this->_searchableAttributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        return $this->_searchableAttributes;
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
        $cacheId = 'elasticsearch_category_index_properties_' . $store->getId();
        if (Mage::app()->useCache('config')) {
            $properties = Mage::app()->loadCache($cacheId);
            if ($properties) {
                return unserialize($properties);
            }
        }

        $properties = array();

        $attributes = $this->getSearchableAttributes($store);
        foreach ($attributes as $attribute) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            $key = $attribute->getAttributeCode();
            $attribute->setIsSearchable(true);
            $properties[$key] = $this->getAttributeProperties($attribute, $store);
        }

        // Add URL field
        $properties['_url'] = array(
            'type' => 'string',
            'store' => true,
            'index' => 'no',
        );

        // Add category path field
        $properties['_path'] = array(
            'type' => 'string',
            'store' => true,
            'index' => 'no',
        );

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

    /**
     * Retrieve all categories of given store with path names
     *
     * @param mixed $store
     * @return Mage_Catalog_Model_Resource_Collection_Abstract
     * @throws Mage_Core_Exception
     */
    public function getCategoriesWithPathNames($store = null)
    {
        $store = Mage::app()->getStore($store);
        if (!isset($this->_categoriesWithPathNames[$store->getId()])) {
            $collection = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('name')
                ->setStoreId($store->getId());
            foreach ($collection as $category) {
                $category->setPathNames(new ArrayObject());
                $pathIds = array_slice($category->getPathIds(), 2);
                if (!empty($pathIds)) {
                    foreach ($pathIds as $pathId) {
                        if ($item = $collection->getItemById($pathId)) {
                            $category->getPathNames()->append($item->getName());
                        }
                    }
                }
            }

            $this->_categoriesWithPathNames[$store->getId()] = $collection;
        }

        return $this->_categoriesWithPathNames[$store->getId()];
    }

    /**
     * Return given category path name with specified separator
     *
     * @param Mage_Catalog_Model_Category $category
     * @param string $separator
     * @return string
     */
    public function getCategoryPathName(Mage_Catalog_Model_Category $category, $separator = ' > ')
    {
        $categoryWithPathNames = $this->getCategoriesWithPathNames($category->getStore())
            ->getItemById($category->getId());

        if ($categoryWithPathNames) {
            return implode($separator, (array) $categoryWithPathNames->getPathNames());
        }

        return $category->getName();
    }
}