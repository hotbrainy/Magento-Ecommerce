<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Helper_Indexer_Product extends Bubble_Elasticsearch_Helper_Indexer_Abstract
{
    /**
     * Searchable attributes
     *
     * @var array
     */
    protected $_searchableAttributes;

    /**
     * @var string
     */
    protected $_blockClass = 'Bubble_Elasticsearch_Block_Autocomplete_Product';

    /**
     * Export products according to optional filters
     *
     * @param array $filters
     * @param int $split
     * @return array
     */
    public function export($filters = array(), $split = 2000)
    {
        set_time_limit(0); // export might be a bit slow

        $result             = array();
        $product            = Mage::getModel('catalog/product');
        $attributesByTable  = $product->getResource()->loadAllAttributes($product)->getAttributesByTable();
        $mainTable          = $product->getResource()->getTable('catalog_product_entity');
        $resource           = $this->_getResource();
        $adapter            = $this->_getAdapter();
        $product            = new Varien_Object();
        $isEnterprise       = Mage::helper('core')->isModuleEnabled('Enterprise_UrlRewrite');

        foreach (Mage::app()->getStores() as $store) {
            /** @var Mage_Core_Model_Store $store */
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

            $categoryNames          = $this->getCategoryNames($store);

            $taxHelper              = Mage::helper('tax');
            $priceDisplayType       = $taxHelper->getPriceDisplayType($store);
            $showPriceIncludingTax  = in_array($priceDisplayType, array(
                Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX,
                Mage_Tax_Model_Config::DISPLAY_TYPE_BOTH,
            ));
            $defaultGroupId         = Mage_Customer_Model_Group::NOT_LOGGED_IN_ID;
            $customerTaxClass       = Mage::getModel('customer/group')->getTaxClassId($defaultGroupId);

            $this->handleMessage(' > Exporting products of store %s', $store->getCode());

            $result[$storeId] = array();
            $select = $adapter->select()->from(array('e' => $mainTable), 'entity_id');

            // Filter products that are enabled for current store website
            $select->join(
                array('product_website' => $resource->getTableName('catalog/product_website')),
                'product_website.product_id = e.entity_id AND ' . $adapter->quoteInto('product_website.website_id = ?', $store->getWebsiteId()),
                array()
            );

            // Index only in stock products if showing out of stock products is not needed
            if (!$this->isIndexOutOfStockProducts($store)) {
                $manageStock = $store->getConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_MANAGE_STOCK);
                $condArr = array(
                    'stock.use_config_manage_stock = 0 AND stock.manage_stock = 1 AND stock.is_in_stock = 1',
                    'stock.use_config_manage_stock = 0 AND stock.manage_stock = 0',
                );
                if ($manageStock) {
                    $condArr[] = 'stock.use_config_manage_stock = 1 AND stock.is_in_stock = 1';
                } else {
                    $condArr[] = 'stock.use_config_manage_stock = 1';
                }
                $cond = '((' . implode(') OR (', $condArr) . '))';
                $select->join(
                    array('stock' => $resource->getTableName('cataloginventory_stock_item')),
                    '(stock.product_id = e.entity_id) AND ' . $cond,
                    array()
                );
            }

            if (!empty($filters)) {
                foreach ($filters as $field => $value) {
                    if ($field == 'store_id' || $value === null) {
                        continue;
                    }
                    if (is_array($value)) {
                        $select->where("e.$field IN (?)", $value);
                    } else {
                        $select->where("e.$field = ?", $value);
                    }
                }
            }

            // Handle enabled products
            $attributeId = Mage::getSingleton('eav/entity_attribute')
                ->getIdByCode(Mage_Catalog_Model_Product::ENTITY, 'status');
            if ($attributeId) {
                $enabled = Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
                $select->join(
                    array('status' => $resource->getTableName('catalog_product_entity_int')),
                    "status.attribute_id = $attributeId AND status.entity_id = e.entity_id",
                    array()
                );
                $select->where('status.value = ?', $enabled);
                $select->where('status.store_id IN (?)', array(0, $storeId));
            }

            // Fetch entity ids that match
            $allEntityIds = $adapter->fetchCol($select);
            $allEntityIds = array_unique($allEntityIds);
            $this->handleMessage(' > Found %d products', count($allEntityIds));

            $allEntityIds = array_chunk($allEntityIds, $split);
            $countChunks = count($allEntityIds);
            if ($countChunks > 1) {
                $this->handleMessage(' > Split products array into %d chunks for better performances', $split);
            }
            $attrOptionLabels = array();

            // Loop through products
            foreach ($allEntityIds as $i => $entityIds) {
                if ($countChunks > 1) {
                    $this->handleMessage(' > %d/%d', $i + 1, $countChunks);
                }
                $products = array();
                foreach ($attributesByTable as $table => $allAttributes) {
                    $allAttributes = array_chunk($allAttributes, 25);
                    foreach ($allAttributes as $attributes) {
                        $select = $adapter->select()
                            ->from(array('e' => $mainTable), array('id' => 'entity_id', 'sku', 'type_id'));

                        foreach ($attributes as $attribute) {
                            if (!$this->isAttributeIndexable($attribute)) {
                                continue;
                            }
                            $attributeId = $attribute->getAttributeId();
                            $attributeCode = $attribute->getAttributeCode();

                            if (!isset($attrOptionLabels[$attributeCode]) && $this->isAttributeUsingOptions($attribute)) {
                                $options = $attribute->setStoreId($storeId)
                                    ->getSource()
                                    ->getAllOptions();
                                foreach ($options as $option) {
                                    if (!$option['value']) {
                                        continue;
                                    }
                                    $attrOptionLabels[$attributeCode][$option['value']] = $option['label'];
                                }
                            }
                            $alias1 = $attributeCode . '_default';
                            $select->joinLeft(
                                array($alias1 => $adapter->getTableName($table)),
                                "$alias1.attribute_id = $attributeId AND $alias1.entity_id = e.entity_id AND $alias1.store_id = 0",
                                array()
                            );
                            $alias2 = $attributeCode . '_store';
                            $valueExpr = $adapter->getCheckSql("$alias2.value IS NULL", "$alias1.value", "$alias2.value");
                            $select->joinLeft(
                                array($alias2 => $adapter->getTableName($table)),
                                "$alias2.attribute_id = $attributeId AND $alias2.entity_id = e.entity_id AND $alias2.store_id = {$store->getId()}",
                                array($attributeCode => $valueExpr)
                            );
                        }

                        $select->where('e.entity_id IN (?)', $entityIds);
                        $query = $adapter->query($select);

                        while ($row = $query->fetch()) {
                            $row = array_filter($row, 'strlen');
                            $row['id'] = (int) $row['id'];
                            $productId = $row['id'];
                            if (!isset($products[$productId])) {
                                $products[$productId] = array();
                            }
                            foreach ($row as $code => &$value) {
                                if (isset($attributesByTable[$table][$code])) {
                                    $value = $this->_formatValue($attributesByTable[$table][$code], $value, $store);
                                }
                                if (isset($attrOptionLabels[$code])) {
                                    if (is_array($value)) {
                                        $label = array();
                                        foreach ($value as $val) {
                                            if (isset($attrOptionLabels[$code][$val])) {
                                                $label[] = $attrOptionLabels[$code][$val];
                                            }
                                        }
                                        if (!empty($label)) {
                                            $row[$code] = $label;
                                        }
                                    } elseif (isset($attrOptionLabels[$code][$value])) {
                                        $row[$code] = $attrOptionLabels[$code][$value];
                                    }
                                }
                            }
                            unset($value);
                            $products[$productId] = array_merge($products[$productId], $row);
                        }
                    }
                }

                // Add parent products in order to retrieve products that have associated products
                $key = '_parent_ids';
                $select = $adapter->select()
                    ->from($resource->getTableName('catalog_product_relation'),
                        array('parent_id', 'child_id'))
                    ->where('child_id IN (?)', $entityIds);
                $query = $adapter->query($select);
                while ($row = $query->fetch()) {
                    $productId = $row['child_id'];
                    if (!isset($products[$productId][$key])) {
                        $products[$productId][$key] = array();
                    }
                    $products[$productId][$key][] = (int) $row['parent_id'];
                }

                // Add categories
                $key = '_categories';
                $columns = array(
                    'product_id'    => 'product_id',
                    'category_ids'  => new Zend_Db_Expr(
                        "TRIM(
                            BOTH ',' FROM CONCAT(
                                TRIM(BOTH ',' FROM GROUP_CONCAT(IF(is_parent = 0, category_id, '') SEPARATOR ',')),
                                ',',
                                TRIM(BOTH ',' FROM GROUP_CONCAT(IF(is_parent = 1, category_id, '') SEPARATOR ','))
                            )
                        )"),
                );
                $select = $adapter->select()
                    ->from(array($resource->getTableName('catalog_category_product_index')), $columns)
                    ->where('product_id IN (?)', $entityIds)
                    ->where('store_id = ?', $storeId)
                    ->where('category_id > 1') // ignore global root category
                    ->where('category_id != ?', $store->getRootCategoryId()) // ignore store root category
                    ->group('product_id');
                $query = $adapter->query($select);
                while ($row = $query->fetch()) {
                    $categoryIds = explode(',', $row['category_ids']);
                    if (empty($categoryIds)) {
                        continue;
                    }
                    $productId = $row['product_id'];
                    if (!isset($products[$productId][$key])) {
                        $products[$productId][$key] = array();
                    }
                    foreach ($categoryIds as $categoryId) {
                        if (isset($categoryNames[$categoryId])) {
                            $products[$productId][$key][] = $categoryNames[$categoryId];
                        }
                    }
                    $products[$productId][$key] = array_values(array_unique($products[$productId][$key]));
                }

                // Add prices
                $key = '_prices';
                $least = $adapter->getLeastSql(array('prices.min_price', 'prices.tier_price'));
                $minimalExpr = $adapter->getCheckSql('prices.tier_price IS NOT NULL', $least, 'prices.min_price');
                $cols = array(
                    'entity_id', 'price', 'final_price',
                    'minimal_price' => $minimalExpr,
                    'min_price', 'max_price', 'tier_price'
                );
                $select = $adapter->select()
                    ->from(array('prices' => $resource->getTableName('catalog_product_index_price')), $cols)
                    ->where('prices.entity_id IN (?)', $entityIds)
                    ->where('prices.website_id = ?', $store->getWebsiteId())
                    ->where('prices.customer_group_id = ?', $defaultGroupId);
                $query = $adapter->query($select);
                while ($row = $query->fetch()) {
                    $productId = $row['entity_id'];
                    unset($row['entity_id']);
                    $row['price']       = (float) $row['price'];
                    $row['final_price'] = (float) $row['final_price'];
                    if (null !== $row['minimal_price']) {
                        $row['minimal_price'] = (float) $row['minimal_price'];
                    }
                    if (null !== $row['min_price']) {
                        $row['min_price'] = (float) $row['min_price'];
                    }
                    if (null !== $row['max_price']) {
                        $row['max_price'] = (float) $row['max_price'];
                    }
                    if (null !== $row['tier_price']) {
                        $row['tier_price'] = (float) $row['tier_price'];
                    }
                    if (isset($row['group_price']) && null !== $row['group_price']) {
                        $row['group_price'] = (float) $row['group_price'];
                    }
                    if (isset($products[$productId]['tax_class_id'])) {
                        $taxClassId = $products[$productId]['tax_class_id'];
                        if ($taxClassId) {
                            $product->setTaxClassId($taxClassId);
                            foreach ($row as &$price) {
                                $price = $taxHelper->getPrice(
                                    $product, $price, $showPriceIncludingTax, null, null, $customerTaxClass, $store
                                );
                            }
                            unset($price);
                        }
                    }
                    $products[$productId][$key] = $row;
                }

                // Add product URL
                $key = '_url';
                $suffix = '';

                if ($isEnterprise) {
                    $entityType = Enterprise_Catalog_Model_Product::URL_REWRITE_ENTITY_TYPE;
                    $select = $adapter->select()
                        ->from(
                            array('url_key' => $resource->getTableName(array('catalog/product', 'url_key'))),
                            array('product_id' => 'entity_id')
                        )
                        ->join(
                            array('url_rewrite' => $resource->getTableName('enterprise_urlrewrite/url_rewrite')),
                            'url_key.value_id = url_rewrite.value_id AND url_rewrite.entity_type = ' . $entityType,
                            array('request_path')
                        )
                        ->where('entity_id IN (?)', $entityIds);
                    $suffix = $store->getConfig(Mage_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_SUFFIX);
                    if ($suffix) {
                        $suffix = '.' . $suffix;
                    }
                } else {
                    $select = $adapter->select()
                        ->from($resource->getTableName('core_url_rewrite'), array('product_id', 'request_path'))
                        ->where('store_id = ?', $storeId)
                        ->where('category_id IS NULL')
                        ->where("(options IS NULL OR options = '')")
                        ->where('product_id IN (?)', $entityIds);
                }

                $query = $adapter->query($select);
                while ($row = $query->fetch()) {
                    $productId = $row['product_id'];
                    $row['product_id'] = (int) $row['product_id'];
                    $products[$productId][$key] = $store->getBaseUrl() . $row['request_path'] . $suffix;
                }

                if (!empty($products)) {
                    $result[$storeId] = array_merge($result[$storeId], $products);
                }
            }

            $this->handleMessage(' > Products exported');
        }

        return $result;
    }

    /**
     * Returns additional fields to add to Elasticsearch query
     *
     * @return array
     */
    public function getAdditionalFields()
    {
        return array('_parent_ids'); // product id is already implicitly included
    }

    /**
     * Retrieve store category names mapping
     *
     * @param null $store
     * @return array
     */
    public function getCategoryNames($store = null)
    {
        $store = Mage::app()->getStore($store);
        $adapter = $this->_getAdapter();
        $attributeId = Mage::getSingleton('eav/entity_attribute')
            ->getIdByCode(Mage_Catalog_Model_Category::ENTITY, 'name');
        $select = $adapter->select()
            ->from($this->_getResource()->getTableName('catalog_category_entity_varchar'), array('entity_id', 'value'))
            ->where('attribute_id = ?', $attributeId) // only category name attribute values
            ->where('store_id IN (?)', array(0, $store->getId())) // use default value if not overriden in store view scope
            ->order(array('entity_id ASC', 'store_id ASC')); // used to handle store view overrides

        return $adapter->fetchPairs($select);
    }

    /**
     * Retrieves all searchable product attributes
     * Possibility to filter attributes by backend type
     *
     * @param array $backendType
     * @return array
     */
    public function getSearchableAttributes($backendType = null)
    {
        if (null === $this->_searchableAttributes) {
            $this->_searchableAttributes = array();
            $entityType = $this->getEavConfig()->getEntityType('catalog_product');
            $entity = $entityType->getEntity();

            /* @var Mage_Catalog_Model_Resource_Product_Attribute_Collection $productAttributeCollection */
            $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
                ->setEntityTypeFilter($entityType->getEntityTypeId())
                ->addVisibleFilter()
                ->addToIndexFilter(true);

            $attributes = $productAttributeCollection->getItems();
            foreach ($attributes as $attribute) {
                /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
                $attribute->setEntity($entity);
                $this->_searchableAttributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        if (null !== $backendType) {
            $backendType = (array) $backendType;
            $attributes = array();
            foreach ($this->_searchableAttributes as $attribute) {
                /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
                if (in_array($attribute->getBackendType(), $backendType)) {
                    $attributes[$attribute->getAttributeCode()] = $attribute;
                }
            }

            return $attributes;
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
        $cacheId = 'elasticsearch_product_index_properties_' . $store->getId();
        if (Mage::app()->useCache('config')) {
            $properties = Mage::app()->loadCache($cacheId);
            if ($properties) {
                return unserialize($properties);
            }
        }

        $properties = array();
        $indexSettings = $this->getStoreIndexSettings($store);

        $attributes = $this->getSearchableAttributes(array('varchar', 'int'));
        foreach ($attributes as $attribute) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            if ($this->isAttributeIndexable($attribute)) {
                $key = $attribute->getAttributeCode();
                $properties[$key] = $this->getAttributeProperties($attribute, $store);
            }
        }

        $attributes = $this->getSearchableAttributes('text');
        foreach ($attributes as $attribute) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            $key = $attribute->getAttributeCode();
            $properties[$key] = $this->getAttributeProperties($attribute, $store);
        }

        $attributes = $this->getSearchableAttributes(array('static', 'varchar', 'decimal', 'datetime'));
        foreach ($attributes as $attribute) {
            /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
            $key = $attribute->getAttributeCode();
            if ($this->isAttributeIndexable($attribute) && !isset($properties[$key])) {
                $type = $this->getAttributeType($attribute);
                if ($type === 'option') {
                    continue;
                }
                $weight = $attribute->getSearchWeight();
                $properties[$key] = array(
                    'type' => $type,
                    'include_in_all' => (bool) $attribute->getIsSearchable(),
                );
                if ($weight) {
                    $properties[$key]['boost'] = intval($weight); // boost at query time
                }
                if ($key == 'sku') {
                    $properties[$key]['fields'] = array(
                        'keyword' => array(
                            'type' => 'string',
                            'analyzer' => 'keyword',
                        ),
                        'prefix' => array(
                            'type' => 'string',
                            'analyzer' => 'keyword_prefix',
                            'search_analyzer' => 'keyword',
                        ),
                        'suffix' => array(
                            'type' => 'string',
                            'analyzer' => 'keyword_suffix',
                            'search_analyzer' => 'keyword',
                        ),
                    );
                }
                if ($key == 'price') {
                    $properties[$key]['fields'] = array(
                        'keyword' => array(
                            'type' => 'string',
                            'index' => 'not_analyzed',
                        ),
                    );
                }
                if ($attribute->getBackendType() == 'datetime') {
                    $properties[$key]['format'] = $this->_dateFormat;
                    $properties[$key]['ignore_malformed'] = true;
                }
            }
        }

        // Add categories field
        $properties['_categories'] = array(
            'type' => 'string',
            'include_in_all' => true,
            'analyzer' => 'std',
        );
        if (isset($indexSettings['analysis']['analyzer']['language'])) {
            $properties['_categories']['analyzer'] = 'language';
        }

        // Add parent_ids field
        $properties['_parent_ids'] = array(
            'type' => 'integer',
            'store' => true,
            'index' => 'no',
        );

        // Add URL field
        $properties['_url'] = array(
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
     * @param null $store
     * @return bool
     */
    public function isIndexOutOfStockProducts($store = null)
    {
        return Mage::getStoreConfigFlag(Mage_CatalogInventory_Helper_Data::XML_PATH_SHOW_OUT_OF_STOCK, $store);
    }
}