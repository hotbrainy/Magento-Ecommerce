<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


class Amasty_Shopby_Model_Url_Builder
{
    /** @var  string */
    public $moduleName;

    /** @var  int */
    public $mode;

    /** @var  array */
    protected $query;

    /** @var  array */
    protected $effectiveQuery;

    /** @var  string */
    protected $brandAttributeCode;

    protected $allowAjaxFlag = false;

    /** @var Mage_Catalog_Model_Category|int */
    public $category;

	/** @var int */
	protected $filterUrlSortMode = Amasty_Shopby_Model_Source_Url_SortMode::MODE_CODE;

	/**
	 * @var array
	 */
	protected  $attributesPositions;

    public function reset()
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');

        // Destination parameters
        $this->moduleName = Mage::app()->getRequest()->getModuleName();
        if ($this->moduleName == 'cms') {
            $this->clearModule();
        }
        $this->category = $helper->getCurrentCategory();
        $this->query = Mage::app()->getRequest()->getQuery();
        $this->mode = Mage::getStoreConfig('amshopby/seo/urls');

        // Configuration parameters
        $this->brandAttributeCode = Mage::getStoreConfig('amshopby/brands/attr');
        $this->filterUrlSortMode = Mage::getStoreConfig('amshopby/seo/sort_attributes_in_url');
		$this->attributesPositions = Mage::helper('amshopby/attributes')->getPositionsAttributes();
    }

    public function clearQuery()
    {
        $query = array();
        if ($this->isSomeSearch() && isset($this->query['q'])) {
            $query['q'] = $this->query['q'];
        }
        $this->query = $query;
    }

    public function clearPagination()
    {
        $pager = Mage::getBlockSingleton('page/html_pager');
        if (is_object($pager)) {
            $var = $pager->getPageVarName();
            if (isset($this->query[$var])) {
                unset($this->query[$var]);
            }
        }
    }

    public function clearCategory()
    {
        $this->category = Mage::app()->getStore()->getRootCategoryId();
    }

    public function clearModule()
    {
        $this->moduleName = 'amshopby';
    }

    public function changeQuery(array $delta)
    {
        $this->query = array_merge($this->query, $delta);
    }

    /**
     * "is_ajax=1" parameter should be added by JS in regular case
     *
     * @param bool $allow
     */
    public function setAllowAjaxFlag($allow)
    {
        $this->allowAjaxFlag = $allow;
    }

    public function getUrl()
    {
        $this->updateEffectiveQuery();

        $paramPart = $this->getParamPart();
        $basePart = $this->getBasePart($paramPart);

        $url = $basePart . $paramPart;
        $url = preg_replace('|(^:)/{2,}|', '$1/', $url);

        return $url;
    }

    protected function updateEffectiveQuery()
    {
        $this->effectiveQuery = $this->query;

        $this->processCategoryParameter();
        $this->excludeParams();
        $this->cleanNulls();
        $this->sortQuery();
        $this->detectMultiselectParam();
    }

    protected function processCategoryParameter()
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        if ($helper->getCategoriesMultiselectMode()) {
            // No action needed in multiselect mode: it`s already ok.
        } else {
            if (isset($this->effectiveQuery['cat'])) {
                $goToCategory = !($this->isNewOrSale() || $this->getCurrentLandingKey() || $this->isSomeSearch());
                if ($goToCategory) {
                    $this->category = Mage::getModel('catalog/category')->load($this->effectiveQuery['cat']);
                    $this->effectiveQuery['cat'] = null;
                }
            }
        }
    }

    protected function excludeParams()
    {
        $excludeParamsStr = trim(Mage::getStoreConfig('amshopby/seo/query_param_exclude'));
        if ($excludeParamsStr != '') {
            $excludeParams = array_intersect(explode(',', $excludeParamsStr), array_keys($this->effectiveQuery));
            foreach ($excludeParams as $param) {
                unset($this->effectiveQuery[$param]);
            }
        }

        if (isset($this->effectiveQuery['is_ajax']) && !$this->allowAjaxFlag) {
            unset($this->effectiveQuery['is_ajax']);
        }
    }

    protected function cleanNulls()
    {
        foreach ($this->effectiveQuery as $k => &$v){
            if (is_null($v) || $v === '') {
                unset($this->effectiveQuery[$k]);
                continue;
            }
        }
    }

    protected function sortQuery()
    {
        foreach ($this->effectiveQuery as &$v){
            //sort values to avoid duplicate content
            if (is_array($v)) {
                sort($v);
            }
        }
		uksort($this->effectiveQuery, array($this, 'compareParamsPriority'));
    }

    protected function compareParamsPriority($a, $b)
    {
        if ($a == 'is_ajax') {
            return 1;
        } else if ($b == 'is_ajax') {
            return -1;
        }

		if($this->filterUrlSortMode == Amasty_Shopby_Model_Source_Url_SortMode::MODE_POSITION) {
			if(isset($this->attributesPositions[$a]) && isset($this->attributesPositions[$b])) {
				if($this->attributesPositions[$a] < $this->attributesPositions[$b]){
					return -1;
				} elseif($this->attributesPositions[$a] == $this->attributesPositions[$b]) {
					return 0;
				} else {
					return 1;
				}
			}
		} else {
            if ($a == $this->brandAttributeCode) {
                return -1;
            } else if ($b == $this->brandAttributeCode) {
                return 1;
            }
        }

        return strcmp($a, $b);
    }

    protected function detectMultiselectParam()
    {
        $paramName = Mage::getStoreConfig('amshopby/seo/query_param');
        if ($paramName) {
            $foundMultipleValues = false;
            foreach ($this->query as $code => $v) {
                $isMultipleSelectedArray = is_array($v) && count($v) > 1;
                $isMultipleSelectedString = is_string($v) && preg_match('@\d+,[\d,]*\d@', $v);
                $isDecimal = $this->getUrlHelper()->isDecimal($code);

                if (($isMultipleSelectedArray || $isMultipleSelectedString) && !$isDecimal) {
                    $foundMultipleValues = true;
                    break;
                }
            }
            if ($foundMultipleValues){
                $this->effectiveQuery[$paramName] = 'true';
            }
            else {
                unset($this->effectiveQuery[$paramName]);
            }
        }
    }

    protected function getParamPart()
    {
        $seoParts = array();
        $query = array();
        // add attributes as keys, not as ids
        if ($this->mode && !$this->isSomeSearch()) {
            $options = $this->getUrlHelper()->getAllFilterableOptionsAsHash();
            foreach ($this->effectiveQuery as $origAttrCode => $ids){
                $attrCode = str_replace(array('_', '-'), Mage::getStoreConfig('amshopby/seo/special_char'), $origAttrCode);

                if (isset($options[$attrCode]) && $options[$attrCode]){ // it is filterable attribute
                    if ($this->mode == Amasty_Shopby_Model_Source_Url_Mode::MODE_SHORT) {
                        $part = $this->getUrlHelper()->_formatAttributePartShort($attrCode, $ids);
                    } else {
                        $part = $this->getUrlHelper()->_formatAttributePartMultilevel($attrCode, $ids);
                    }

                    if (strlen($part)) {
                        $seoParts[] = $part;
                    }
                }
                else {
                    $query[$origAttrCode] = $ids; // it is pager or smth else
                }
            }
        } else {
            $query = $this->effectiveQuery;
        }

        $glue = ($this->mode == Amasty_Shopby_Model_Source_Url_Mode::MODE_SHORT) ? Mage::getStoreConfig('amshopby/seo/option_char') : '/';
        $result = implode($glue, $seoParts);
        if (strlen($result)) {
            $result = $this->getUrlHelper()->checkAddSuffix($result);
        }

        // add other params as query string if any
        $query = http_build_query($query);
        if (strlen($query)){
            $result .= '?' . $query;
        }

        return $result;
    }

    protected function getBasePart($paramPart)
    {
        $rootId = (int) Mage::app()->getStore()->getRootCategoryId();
        $reservedKey = Mage::getStoreConfig('amshopby/seo/key');
        $seoAttributePartExist = strlen($paramPart) && strpos($paramPart, '?') !== 0;

        $isSecure = Mage::app()->getStore()->isCurrentlySecure();
        $base = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, $isSecure);

        if ($this->isCatalogSearch()){
            $url = $base . 'catalogsearch/result/';
        }
        elseif ($this->isNewOrSale()) {
            $url = $base . $this->moduleName;
        }
        elseif ($this->getCurrentLandingKey()) {
            $url = $base . $this->getCurrentLandingKey();

            if ($seoAttributePartExist) {
                $url.= '/';
            } else {
                $url = $this->getUrlHelper()->checkAddSuffix($url);
            }
        }
        elseif ($this->isCategorySearch()) {
            $url = $base . 'categorysearch/categorysearch/search/';
        }
        elseif ($this->moduleName == 'cms' && $this->getCategoryId() == $rootId) { // homepage,
            $hasFilter = false;
            if (Mage::getStoreConfig('amshopby/block/ajax')) {
                $hasFilter = true;
            }
            if (!$hasFilter) {
                foreach (array_keys($this->query) as $k){
                    if (!in_array($k, array('p','mode','order','dir','limit')) && false === strpos('__', $k)){
                        $hasFilter = true;
                        break;
                    }
                }
            }

            // homepage filter links
            if ($this->isUrlKeyMode() && $hasFilter){
                $url = $base . $reservedKey . '/';
            }
            // homepage sorting/paging url
            else {
                $url = $base;
            }
        }
        elseif ($this->getCategoryId() == $rootId) {
            $url = $base;

            switch ($this->mode) {
                case Amasty_Shopby_Model_Source_Url_Mode::MODE_DISABLED:
                    $needUrlKey = true;
                    break;
                case Amasty_Shopby_Model_Source_Url_Mode::MODE_MULTILEVEL:
                    $needUrlKey = !$this->isBrandPage();
                    break;
                case Amasty_Shopby_Model_Source_Url_Mode::MODE_SHORT:
                    $needUrlKey = !$seoAttributePartExist;
                    break;
                default:
                    $needUrlKey = true;
            }
            if ($needUrlKey) {
                $url.= $reservedKey;
                if ($seoAttributePartExist) {
                    $url .=  '/';
                }
            }
        }
        else { // we have a valid category
            $url = $this->getCategoryObject()->getUrl();
            $pos = strpos($url,'?');
            $url = $pos ? substr($url, 0, $pos) : $url;

            if ($seoAttributePartExist) {
                $url = $this->getUrlHelper()->checkRemoveSuffix($url);
                if ($this->isUrlKeyMode()) {
                    $url .= '/' . $reservedKey;
                }
                $url.= '/';
            }

        }

        return $url;
    }

    protected function isBrandPage()
    {
        $attrCode = trim(Mage::getStoreConfig('amshopby/brands/attr'));
        $isAttributeRequested = $attrCode && isset($this->effectiveQuery[$attrCode]);

        $isBrandPage = $this->moduleName == 'amshopby' && $isAttributeRequested;
        return $isBrandPage;
    }

    /**
     * @return int
     */
    protected function getCategoryId()
    {
        return is_object($this->category) ? $this->category->getId() : $this->category;
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    protected function getCategoryObject()
    {
        if (!is_object($this->category)) {
            $this->category = Mage::getModel('catalog/category')->load($this->category);
        }
        return $this->category;
    }

    protected function isNewOrSale()
    {
        return in_array($this->moduleName, array('catalognew', 'catalogsale'));
    }

    protected function isSomeSearch()
    {
        return $this->isCatalogSearch() || $this->isCategorySearch();
    }

    protected function isCatalogSearch()
    {
        return in_array($this->moduleName, array('sqli_singlesearchresult', 'catalogsearch'));
    }

    protected function isCategorySearch()
    {
        return $this->moduleName == 'categorysearch';
    }

    protected function getCurrentLandingKey()
    {
        return Mage::app()->getRequest()->getParam('am_landing');
    }

    protected function getUrlHelper()
    {
        /** @var Amasty_Shopby_Helper_Url $helper */
        $helper = Mage::helper('amshopby/url');
        return $helper;
    }

    protected function isUrlKeyMode()
    {
        return $this->mode == Amasty_Shopby_Model_Source_Url_Mode::MODE_MULTILEVEL || $this->mode == Amasty_Shopby_Model_Source_Url_Mode::MODE_DISABLED;
    }
}
