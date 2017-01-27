<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * @method array getCategories()
 * @method setCategories(array $ids)
 */
class Amasty_Shopby_Model_Catalog_Layer_Filter_Category extends Amasty_Shopby_Model_Catalog_Layer_Filter_Category_Adapter
{
    /**
     * Display Types
     */
    const DT_DEFAULT    = 0;
    const DT_DROPDOWN   = 1;
    const DT_WSUBCAT    = 2;
    const DT_STATIC2LVL = 3;
    const DT_ADVANCED   = 4;

    protected $includedIds;
    protected $excludedIds;

    protected static $_appliedState = FALSE;

    /** @var  Amasty_Shopby_Model_Url_Builder */
    protected $urlBuilder;

    protected $_displayType = null;
    protected $_advancedCollection = null;


    public function __construct()
    {
        parent::__construct();

        /** @var Amasty_Shopby_Model_Url_Builder $urlBuilder */
        $urlBuilder = Mage::getModel('amshopby/url_builder');
        $urlBuilder->reset();
        $urlBuilder->clearPagination();
        $this->urlBuilder = $urlBuilder;
        $this->_displayType = Mage::getStoreConfig('amshopby/general/categories_type');

        // For use in Advanced Categories block
        Mage::register('amshopby_category_filter_model', $this, true);
    }

    public function getItemsCount()
    {
        $items = self::DT_ADVANCED == $this->_displayType ? $this->getAdvancedCollection() : $this->getItems();
        return count($items);
    }

    public function getAdvancedCollection()
    {
        if (is_null($this->_advancedCollection)) {
            $category = $this->_getDataHelper()->getCurrentCategory();
            $startFrom = Mage::getStoreConfig('amshopby/advanced_categories/start_category');
            switch ($startFrom) {
                case Amasty_Shopby_Model_Source_Category_Start::START_CHILDREN:
                    break;
                case Amasty_Shopby_Model_Source_Category_Start::START_CURRENT:
                    $parent = $category->getParentCategory();
                    if ($parent) {
                        $category = $parent;
                    }
                    break;
                case Amasty_Shopby_Model_Source_Category_Start::START_ROOT:
                default:
                    $category = Mage::getModel('catalog/category')->load(Mage::app()->getStore()->getRootCategoryId());
            }
            $excludeIds = preg_replace('/[^\d,]+/', '', Mage::getStoreConfig('amshopby/general/exclude_cat'));
            $excludeIds = $excludeIds ? explode(',',  $excludeIds) : array();
            $cats = $this->_getCategoryCollection()->addIdFilter($category->getChildren());
            if(count($excludeIds) > 0) {
                $cats->addFieldToFilter('entity_id', array('nin' => $excludeIds));
            }
            $this->addCounts($cats);

            foreach ($cats as $c) {
                if ($c->getProductCount()) {
                    $this->_advancedCollection = $cats;
                    return $this->_advancedCollection;
                }
            }

            $this->_advancedCollection = array();
        }

        return $this->_advancedCollection;
    }

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $categories
     */
    public function addCounts($categories)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = $this->getLayer()->getProductCollection();
        $select = $collection->getSelect();

        $part = $select->getPart(Varien_Db_Select::FROM);

        $indexReplaced = 0;
        if (isset($part['cat_index'])) {
            $originalIndexPart = $part['cat_index']['joinCondition'];
            $part['cat_index']['joinCondition'] = preg_replace('/cat_index.category_id\s*=\s*\'\d+\'/i', '1', $originalIndexPart, -1, $indexReplaced);
            $select->setPart(Varien_Db_Select::FROM, $part);
        }
        $multipleFilterReplaced = 0;
        if (isset($part['cp'])) {
            $originalMultipleFilterPart = $part['cp']['joinCondition'];
            $part['cp']['joinCondition'] = preg_replace('/cp.category_id\s*IN\s*[\(\),\d]+/i', '1', $originalMultipleFilterPart, -1, $multipleFilterReplaced);
            $select->setPart(Varien_Db_Select::FROM, $part);
        }

        $collection->addCountToCategories($categories);
        if ($indexReplaced) {
            $part['cat_index']['joinCondition'] = $originalIndexPart;
            $select->setPart(Varien_Db_Select::FROM, $part);
        }
        if ($multipleFilterReplaced) {
            $part['cp']['joinCondition'] = $originalMultipleFilterPart;
            $select->setPart(Varien_Db_Select::FROM, $part);
        }
    }

    /**
     * Using for advanced categories only
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    protected function _getCategoryCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
        $collection = Mage::getResourceModel('catalog/category_collection');

        $collection
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('all_children')
            ->addAttributeToSelect('is_anchor')
            ->addAttributeToFilter('is_active', 1)
            ->setOrder('position', 'asc')
            ->joinUrlRewrite();

        return $collection;
    }

    protected function _getItemsData()
    {
        if ($this->_displayType == self::DT_ADVANCED) {
            // Will process in amshopby/advanced block
            return array(0 => 1);
        }

        $startCategory = $this->getStartCategory();
        $recursive = $this->_displayType == self::DT_WSUBCAT || $this->_displayType == self::DT_STATIC2LVL;

        $items = $this->getChildrenData($startCategory, $recursive);
        if ($this->_displayType == self::DT_WSUBCAT) {
            $headingData = $this->getSubcategoriesHeadingData($startCategory);
            $items = array_merge($headingData, $items);
        }

        // Hide one value
        if (Mage::getStoreConfig('amshopby/general/hide_one_value') && count ($items) == 1) {
            $items = array();
        }

        return $items;
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    protected function getStartCategory()
    {
        if (self::DT_STATIC2LVL == $this->_displayType) {
            $result = Mage::getModel('catalog/category')->load($this->getLayer()->getCurrentStore()->getRootCategoryId());
        } else {
            /** @var Amasty_Shopby_Helper_Data $helper */
            $helper = Mage::helper('amshopby/data');
            $result = $helper->getCurrentCategory();
        }

        return $result;
    }

    protected function getSubcategoriesHeadingData(Mage_Catalog_Model_Category $startCategory)
    {
        $data = array();
        $rootId  = $this->getLayer()->getCurrentStore()->getRootCategoryId();

        //Get parent category of the current category
        if ($rootId != $startCategory->getId()) {
            $parent = $startCategory->getParentCategory();
            if ($parent->getId() != $rootId && !$this->isExcluded($parent->getId())){
                $data[] = $this->_prepareItemData($parent, -1);
            }

            //Add current category
            //Workaround to load qty properly
            $current = $this->getChildrenData($startCategory->getParentCategory());
            foreach ($current as $head) {
                if ($head['id'] == $startCategory->getId()) {
                    $data[] = $head;
                    break;
                }
            }
        }

        return $data;
    }

    protected function getChildrenData(Mage_Catalog_Model_Category $start, $recursive = false, $level = 0)
    {
        $categories = $this->prepareChildrenCollection($start->getId());
        $data = array();

        foreach ($categories as $category) {
            /** @var Mage_Catalog_Model_Category $category $id */

            $id = $category->getId();
            if ($this->isExcluded($id))
            {
                continue;
            }

            $itemData = $this->_prepareItemData($category, $level + 1);
            if (is_null($itemData)) {
                continue;
            }

            $data[] = $itemData;

            if ($recursive) {
                $childrenData = $this->getChildrenData($category, false, $level + 1);
                if ($childrenData) {
                    $this->attachChildren($data, $childrenData);
                }
            }
        }
        return $data;
    }

    protected function prepareChildrenCollection($parentId)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection $categories */
        $categories = Mage::getModel('catalog/category')->getCollection();
        $categories->addAttributeToSelect('name');
        $categories->addAttributeToSelect('is_anchor');
        $categories->addAttributeToFilter('parent_id', $parentId);
        $categories->addAttributeToFilter('is_active', 1);
        $categories->setOrder('position', 'asc');

        $this->addCounts($categories);
        return $categories;
    }

    protected function attachChildren(&$data, $childrenData)
    {
        $currentIndex = count($data) - 1;

        foreach ($childrenData as $childData) {
            if ($childData['is_selected']) {
                $data[$currentIndex]['is_child_selected'] = true;
                break;
            }
        }

        $data[$currentIndex]['has_children'] = true;
        $data = array_merge($data, $childrenData);
    }

    protected function isExcluded($id)
    {
        if (is_null($this->excludedIds)) {
            $exclude = Mage::getStoreConfig('amshopby/general/exclude_cat');
            if ($exclude){
                $this->excludedIds = explode(',', preg_replace('/[^\d,]+/','', $exclude));
            }
            else {
                $this->excludedIds = array();
            }
        }
        if (in_array($id, $this->excludedIds)) {
            return true;
        }

        if (is_null($this->includedIds)) {
            $include = Mage::getStoreConfig('amshopby/general/include_cat');
            if ($include){
                $this->includedIds = explode(',', preg_replace('/[^\d,]+/','', $include));
            }
            else {
                $this->includedIds = array();
            }
        }
        if ($this->includedIds && !in_array($id, $this->includedIds)) {
            return true;
        }

        return false;
    }

    protected function _initItems()
    {
        $data  = $this->_getItemsData();
        $items = array();
        foreach ($data as $itemData) {
            if (!$itemData)
                continue;

            $obj = new Varien_Object();
            $obj->setData($itemData);

            $items[] = $obj;
        }
        $this->_items = $items;
        return $this;
    }

    protected function _prepareItemData(Mage_Catalog_Model_Category $category, $level = 1)
    {
        $row = null;
        $isSelected = in_array($category->getId(), $this->getCategories());
        $isFolded   = $level > 1 && $this->getCategory()->getParentId() != $category->getParentId();
        $value = $this->_calculateCategoryValue($category->getId());

        if ($this->_getProductCount($category) || is_null($this->_getProductCount($category))) {
            $row = array(
                'label'       => Mage::helper('core')->htmlEscape($category->getName()),
                'url'         => $this->getCategoryUrl($value),
                'count'       => $this->_getProductCount($category),
                'level'       => $level,
                'id'          => $category->getId(),
                'value'       => $value,
                'parent_id'   => $category->getParentId(),
                'is_folded'   => $isFolded,
                'is_selected' => $isSelected,
            );
        }
        return $row;
    }

    protected function _calculateCategoryValue($catId)
    {
        if ($this->_getDataHelper()->getCategoriesMultiselectMode()) {
            $cats = $this->getCategories();
            $p = array_search($catId, $cats);
            if ($p === false) {
                $cats[] = $catId;
            } else {
                unset($cats[$p]);
            }
            sort($cats);
            return implode(',', $cats);
        } else {
            return $catId;
        }
    }

    protected function getCategoryUrl($value)
    {
        $this->urlBuilder->changeQuery(array('cat' => $value));
        return $this->urlBuilder->getUrl();
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return mixed
     */
    protected function _getProductCount($category)
    {
        if ($this->_getDataHelper()->useSolr()) {
            /** @var Enterprise_Search_Model_Resource_Collection $productCollection */
            $productCollection = $this->getLayer()->getProductCollection();
            $categoriesCount = $productCollection->getFacetedData('category_ids');
            return isset($categoriesCount[$category->getId()]) ? $categoriesCount[$category->getId()] : null;
        } else {
            return $category->getProductCount();
        }
    }

    public function addFacetCondition()
    {
        $key = 'amshopby_facet_added_category';
        if (Mage::registry($key)) {
            return $this;
        }

        $excludeCats = $this->_getDataHelper()->getCategoriesMultiselectMode() && $this->getCategories();
        $prefix = $excludeCats ? '{!ex=catt}' : '';
        $this->getLayer()->getProductCollection()->setFacetCondition($prefix.'category_ids');

        Mage::register($key, true);
        return $this;
    }
    
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        $filter = $request->getParam($this->getRequestVar());
        if (!$filter) {
            $this->setCategories(array());
            return $this;
        }

        $categories = explode(',', $filter);
        $this->setCategories($categories);

        if (!self::$_appliedState) {
            if (count($categories) == 1) {
                $this->_applySingle($filter);
            } else {
                $this->_applyMultiple($categories);
            }

            self::$_appliedState = true;
        }

        return $this;
    }

    protected function _applySingle($id)
    {
        $this->_categoryId = $id;
        Mage::register('current_category_filter', $this->getCategory(), true);
        $this->_appliedCategory = Mage::getModel('catalog/category')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($id);

        if ($this->_isValidCategory($this->_appliedCategory)) {
            $this->getLayer()->getProductCollection()
                ->addCategoryFilter($this->_appliedCategory);

            $this->addState(array($id));
        }
    }

    protected function _applyMultiple(array $ids)
    {
        $deepCategories = $this->_digCategories($ids);

        /** @var Mage_Catalog_Model_Layer $layer */
        $layer = $this->getLayer();
        $products = $layer->getProductCollection();

        if ($this->_getDataHelper()->useSolr()) {
            /** @var Enterprise_Search_Model_Resource_Collection $products */
            $prefix = $this->_getDataHelper()->useSolr() ? '{!tag=catt}' : '';
            $products->addFqFilter(array($prefix.'category_ids' => $ids));
        } else {
            $products->getSelect()->joinInner(
                array('cp' => Mage::getResourceModel('catalog/product')->getTable('catalog/category_product')),
                'cp.product_id = e.entity_id AND cp.category_id IN (' . implode(',', $deepCategories) . ')',
                array()
            );
            $products->getSelect()->distinct();
        }

        $this->addState($ids);
    }

    protected function addState($ids)
    {
        /** @var Mage_Catalog_Model_Resource_Category_Collection $categories */
        $categories = Mage::getModel('catalog/category')->getCollection();
        $categories->addIdFilter($ids);
        $categories->addAttributeToSelect('name');
        $names = $categories->getColumnValues('name');
        $ids = $categories->getColumnValues('category_id');
        if(count($ids) == 0) {
            return;
        }

        $state = $this->_createItem(implode(', ', $names), $ids);

        if(count($ids) > 1) {
            $children = array();
            foreach($ids as $i=>$id) {
                $exclude = $ids;
                unset($exclude[$i]);
                $exclude = implode(',', $exclude);

                $children[] = array(
                    'label' => $names[$i],
                    'url' => $this->getCategoryUrl($exclude),
                );
            }
            if (count($children) > 1) {
                $state->setData('children', $children);
            }
        }

        $this->getLayer()->getState()->addFilter(
            $state
        );
    }

    protected function _digCategories(array $ids)
    {
        $allIds = array_map('intval', $ids);

        do {
            /** @var Mage_Catalog_Model_Resource_Category_Collection $categories */
            $categories = Mage::getModel('catalog/category')->getCollection();
            $categories->addAttributeToFilter('parent_id', array('in' => $ids));
            $ids = $categories->getAllIds();
            $allIds = array_merge($allIds, $ids);
        } while ($ids);

        return $allIds;
    }

    protected function _getDataHelper()
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        return $helper;
    }
}
