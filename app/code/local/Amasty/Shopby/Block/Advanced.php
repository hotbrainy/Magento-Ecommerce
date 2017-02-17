<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

class Amasty_Shopby_Block_Advanced extends Mage_Catalog_Block_Navigation
{
    /** @var  Amasty_Shopby_Model_Url_Builder */
    protected $urlBuilder;

    public function getHtml()
    {
        return $this->_toHtml();
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @param int $level
     * @return string
     */
    public function drawOpenCategoryItem($category, $level = 0)
    {
        if ($this->_isExcluded($category->getId()) || !$category->getIsActive()) {
            return '';
        }

        $cssClass = array(
            'amshopby-cat',
            'level' . $level
        );

        $currentCategory = $this->getDataHelper()->getCurrentCategory();

        if ($currentCategory->getId() == $category->getId()) {
            $cssClass[] = 'active';
        }

        if ($this->isCategoryActive($category)) {
            $cssClass[] = 'parent';
        }

        if($category->hasChildren()) {
            $cssClass[] = 'has-child';
        }


        $productCount = '';
        if ($this->showProductCount()) {
            $productCount = $category->getProductCount();
            if ($productCount > 0) {
                $productCount = '&nbsp;<span class="count">(' . $productCount . ')</span>';
            } else {
                $productCount = '';
            }
        }

        $html = array();
        $html[1] = '<a href="' . $this->getCategoryUrl($category) . '">' . $this->htmlEscape($category->getName()) . $productCount . '</a>';

        $showAll   = Mage::getStoreConfig('amshopby/advanced_categories/show_all_categories');
        $showDepth = Mage::getStoreConfig('amshopby/advanced_categories/show_all_categories_depth');

        $hasChild = false;

        $inPath = in_array($category->getId(), $currentCategory->getPathIds());
        $showAsAll = $showAll && ($showDepth == 0 || $showDepth > $level + 1);
        if ($inPath || $showAsAll) {
            $childrenIds = $category->getChildren();
            $children = $this->_getCategoryCollection()->addIdFilter($childrenIds);
            $this->_getFilterModel()->addCounts($children);
            $children = $this->asArray($children);

            if ($children && count($children) > 0) {
                $hasChild = true;
                $htmlChildren = '';
                foreach($children as $child) {
                    $htmlChildren .= $this->drawOpenCategoryItem($child, $level + 1);
                }

                if($htmlChildren != '') {
                    $cssClass[] = 'expanded';
                    $html[2] = '<ol>' . $htmlChildren . '</ol>';
                }
            }
        }

        $html[0] = sprintf('<li class="%s">', implode(" ", $cssClass));
        $html[3] = '</li>';

        ksort($html);

        if ($category->getProductCount() || ($hasChild && $htmlChildren)) {
            $result = implode('', $html);
        } else {
            $result = '';
        }

        return $result;
    }

    /**
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function getCategoryUrl($category)
    {
        $this->urlBuilder->changeQuery(array('cat' => $category->getId()));
        $url = $this->urlBuilder->getUrl();
        return $url;
    }

    /**
     * I need an array with the index being continunig numbers, so
     * it's possible to check for the previous/next category
     *
     * @param mixed $collection
     *
     * @return array
     */
    public function asArray($collection)
    {
        $array = array();
        foreach ($collection as $item) {
            $array[] = $item;
        }
        return $array;
    }

    /**
     * Get categories of current store, using the max depth setting for the vertical navigation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
     */
    public function getCategories()
    {
        return $this->_getFilterModel()->getAdvancedCollection();
    }

    protected function getDataHelper()
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        return $helper;
    }

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

    public function showProductCount()
    {
        return Mage::getStoreConfigFlag('amshopby/advanced_categories/display_product_count');
    }

    protected function _toHtml()
    {
        $urlBuilder = Mage::getModel('amshopby/url_builder');
        /** @var Amasty_Shopby_Model_Url_Builder $urlBuilder */
        $urlBuilder->reset();
        $urlBuilder->clearPagination();
        $this->urlBuilder = $urlBuilder;

        $html = '';

        $cats = $this->getCategories();

        $storeCategories = $this->asArray($cats);

        if (count($storeCategories) > 0) {
             foreach ($storeCategories as $c) {
                 if (!$this->_isExcluded($c->getId())) {
                    $html .= $this->drawOpenCategoryItem($c, 0);
                 }
             }
        }
        return $html;
    }

    /**
     * @return Amasty_Shopby_Model_Catalog_Layer_Filter_Category
     */
    protected function _getFilterModel()
    {
        return Mage::registry('amshopby_category_filter_model');
    }

    protected function _isExcluded($categoryId)
    {
        if (!$this->hasData('exclude_ids')) {
            $excludeIds = preg_replace('/[^\d,]+/', '', Mage::getStoreConfig('amshopby/general/exclude_cat'));
            $excludeIds = $excludeIds ? explode(',',  $excludeIds) : array();
            $this->setData('exclude_ids', $excludeIds);
        }
        $excludeIds = $this->getData('exclude_ids');
        if (in_array($categoryId, $excludeIds)) {
            return true;
        };

        if (!$this->hasData('include_ids')) {
            $includeIds = preg_replace('/[^\d,]+/', '', Mage::getStoreConfig('amshopby/general/include_cat'));
            $includeIds = $includeIds ? explode(',',  $includeIds) : array();
            $this->setData('include_ids', $includeIds);
        }
        $includeIds = $this->getData('include_ids');
        if ($includeIds && !in_array($categoryId, $includeIds)) {
            return true;
        };

        return false;
    }
}
