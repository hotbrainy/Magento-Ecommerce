<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */ 
class Amasty_Shopby_Block_Catalog_Pager extends Mage_Page_Block_Html_Pager
{
    public function getPagerUrl($params=array())
    {
        return $this->getParentBlock()->getPagerUrl($params);
    }

    public function setupCollection()
    {
        /** @var Mage_Catalog_Model_Layer $layer */
        $layer = Mage::getSingleton('catalog/layer');
        $collection = $layer->getProductCollection();

        $limit = ($this->getLimit() == 'all') ? false : $this->getLimit();
        $collection->setPage($this->getCurrentPage(), $limit);

        $this->setCollection($collection);
    }

    public function handlePrevNextTags()
    {
        if (!Mage::getStoreConfig('amshopby/seo/prev_next')) {
            return;
        }

        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        $category = $helper->getCurrentCategory();
        if (!is_object($category) || $category->getData('display_mode') == Mage_Catalog_Model_Category::DM_PAGE) {
            return;
        }

        /** @var Mage_Page_Block_Html_Head $head */
        $head = $this->getLayout()->getBlock('head');

        if (!$this->isFirstPage()) {
            $head->addLinkRel('prev', $this->getPreviousPageUrl());
        }

        if (!$this->isLastPage()) {
            $head->addLinkRel('next', $this->getNextPageUrl());
        }
    }

    public function getPreviousPageUrl()
    {
        $currentUrl = $this->_getCurrentUrl();
        $prevPageNum = $this->getCurrentPage() - 1;

        $result = preg_replace('/(\W)p=\d+/', '$1p=' . $prevPageNum, $currentUrl);

        return $result;
    }

    public function getNextPageUrl()
    {
        $currentUrl = $this->_getCurrentUrl();
        $nextPageNum = $this->getCurrentPage() + 1;

        $result = preg_replace('/(\W)p=\d+/', '$1p=' . $nextPageNum, $currentUrl, -1, $count);

        if (!$count) {
            $delimiter = (strpos($currentUrl, '?') === false) ? '?' : '&';
            $result.= $delimiter . 'p=' . $nextPageNum;
        }

        return $result;
    }

    protected function _getCurrentUrl()
    {
        /** @var Amasty_Shopby_Helper_Url $helper */
        $helper = Mage::helper('amshopby/url');
        return $helper->getFullUrl();
    }
}