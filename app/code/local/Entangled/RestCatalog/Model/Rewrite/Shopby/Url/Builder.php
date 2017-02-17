<?php
class Entangled_RestCatalog_Model_Rewrite_Shopby_Url_Builder extends Amasty_Shopby_Model_Url_Builder {

//    protected function getBasePart($paramPart)
//    {
//        $rootId = (int) Mage::app()->getStore()->getRootCategoryId();
//        $reservedKey = Mage::getStoreConfig('amshopby/seo/key');
//        $seoAttributePartExist = strlen($paramPart) && strpos($paramPart, '?') !== 0;
//
//        $isSecure = Mage::app()->getStore()->isCurrentlySecure();
//        $base = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, $isSecure);
//
//        if ($this->isCatalogSearch()){
//            $url = $base . 'catalogsearch/result/';
//        }
//        elseif ($this->isNewOrSale()) {
//            $url = $base . $this->moduleName;
//        }
//        elseif ($this->getCurrentLandingKey()) {
//            $url = $base . $this->getCurrentLandingKey();
//
//            if ($seoAttributePartExist) {
//                $url.= '/';
//            } else {
//                $url = $this->getUrlHelper()->checkAddSuffix($url);
//            }
//        }
//        elseif ($this->isCategorySearch()) {
//            $url = $base . 'categorysearch/categorysearch/search/';
//        }
//        elseif ($this->moduleName == 'cms' && $this->getCategoryId() == $rootId) { // homepage,
//            $hasFilter = false;
//            if (Mage::getStoreConfig('amshopby/block/ajax')) {
//                $hasFilter = true;
//            }
//            if (!$hasFilter) {
//                foreach (array_keys($this->query) as $k){
//                    if (!in_array($k, array('p','mode','order','dir','limit')) && false === strpos('__', $k)){
//                        $hasFilter = true;
//                        break;
//                    }
//                }
//            }
//
//            // homepage filter links
//            if ($this->isUrlKeyMode() && $hasFilter){
//                $url = $base . $reservedKey . '/';
//            }
//            // homepage sorting/paging url
//            else {
//                $url = $base;
//            }
//        }
//        elseif ($this->getCategoryId() == $rootId) {
//            $url = $base;
//
//            switch ($this->mode) {
//                case Amasty_Shopby_Model_Source_Url_Mode::MODE_DISABLED:
//                    $needUrlKey = true;
//                    break;
//                case Amasty_Shopby_Model_Source_Url_Mode::MODE_MULTILEVEL:
//                    $needUrlKey = !$this->isBrandPage();
//                    break;
//                case Amasty_Shopby_Model_Source_Url_Mode::MODE_SHORT:
//                    $needUrlKey = !$seoAttributePartExist;
//                    break;
//                default:
//                    $needUrlKey = true;
//            }
//            if ($needUrlKey) {
//                $url.= $reservedKey;
//                if ($seoAttributePartExist) {
//                    $url .=  '/';
//                }
//            }
//        }
//        else { // we have a valid category
//            $url = $this->getCategoryObject()->getUrl();
//            $pos = strpos($url,'?');
//            $url = $pos ? substr($url, 0, $pos) : $url;
//
//            if ($seoAttributePartExist) {
//                $url = $this->getUrlHelper()->checkRemoveSuffix($url);
//                if ($this->isUrlKeyMode()) {
//                    $url .= '/' . $reservedKey;
//                }
//                $url.= '/';
//            }
//
//        }
//
//        return $url;
//    }

}