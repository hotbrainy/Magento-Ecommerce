<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
class Amasty_Shopby_Model_EnterprisePageCache_Processor_Amshopby extends Enterprise_PageCache_Model_Processor_Category
{
    const MAX_VALUES_IN_FILTER_FOR_ALLOW_CACHE = 2;
    public function allowCache(Zend_Controller_Request_Http $request)
    {
        $res = parent::allowCache($request);
        if ($res) {
            $attributes = Mage::helper('amshopby/attributes')->getRequestedFilterCodes();
            foreach($attributes as $k=>$v) {
                $v = explode(',', $v);
                if (count($v) > self::MAX_VALUES_IN_FILTER_FOR_ALLOW_CACHE) {
                    $res = 0;
                    break;
                }
            }
        }
        if($res) {
            $isPriceFilter = $request->getQuery('price');
            if($isPriceFilter) {
                $res = 0;
            }
        }
        if($res) {
            $attributesIsDecimal = Mage::helper('amshopby/attributes')->getDecimalAttributeCodeMap();
            $attributesInRequest = Mage::helper('amshopby/attributes')->getRequestedFilterCodes();
            foreach($attributesInRequest as $attributeCode=>$attributeRequest) {
                if(isset($attributesIsDecimal[$attributeCode]) && $attributesIsDecimal[$attributeCode]) {
                    $res = 0;
                    break;
                }
            }
        }
        return $res;
    }

    protected function _getQueryParams()
    {
        if (is_null($this->_queryParams)) {
            $query = $_GET;
            if(Mage::getStoreConfig('amshopby/seo/urls') != Amasty_Shopby_Model_Source_Url_Mode::MODE_DISABLED) {
                $attributes = Mage::helper('amshopby/attributes')->getRequestedFilterCodes();
                $query = array_diff_assoc($_GET, $attributes);
            }
            $queryParams = array_merge($this->_getSessionParams(), $query);
            ksort($queryParams);
            $this->_queryParams = json_encode($queryParams);
        }

        return $this->_queryParams;
    }


}