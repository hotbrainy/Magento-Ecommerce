<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * @method Amasty_Shopby_Model_Page setCond($serializedCond)
 * @method string getCond()
 * @method Amasty_Shopby_Model_Page setCats($cats)
 * @method string getCats()
 * @method int getCmsBlockId()
 * @method int getBottomCmsBlockId()
 * @method string getMetaDescr()
 * @method string getMetaKw()
 * @method string getMetaTitle()
 * @method Amasty_Shopby_Model_Page setUrl(string $url)
 * @method string getTitle()
 * @method string getDescription()
 * @method string getUrl()
 * @method boolean getUseCat()
 */
class Amasty_Shopby_Model_Page extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('amshopby/page');
    }

    public function getAllFilters($addEmpty=false)
    {
        $collection = Mage::getModel('amshopby/filter')->getResourceCollection()
            ->addTitles();
            
        $values = array();
        if ($addEmpty){
            $values[''] = '';
        }
        foreach ($collection as $row){
            $values[$row->getAttributeCode()] = $row->getFrontendLabel();
        } 
        return $values;
    }

    public function matchCurrentFilters()
    {
        $strict = Mage::getStoreConfig('amshopby/seo/page_match_strict');

        /** @var Amasty_Shopby_Helper_Attributes $attributesHelper */
        $attributesHelper = Mage::helper('amshopby/attributes');
        $requestedExtraFilters = $strict ? $attributesHelper->getRequestedFilterCodes() : null;

        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        $conditions = $this->_getConditions();

        foreach ($conditions as $code => $expected) {
            $actual = $helper->getRequestValues($code);

            if ($strict) {
                unset($requestedExtraFilters[$code]);

                if (array_diff($actual, $expected)) {
                    return false;
                }
            }

            if (array_diff($expected, $actual)) {
                return false;
            }
        }

        if ($strict && $requestedExtraFilters) {
            return false;
        }

        return true;
    }

    protected function _getConditions()
    {
        $conditions = unserialize($this->getCond());
        if (!is_array($conditions)) {
            return array();
        }

        $result = array();

        foreach ($conditions as $k => $v) {
            if (!$v){ // multiselect can be empty
                continue;
            }

            if (is_array($v) && is_numeric($k)) {
                /* Multiple attributes fix */
                $code = $v['attribute_code'];
                $value = $v['attribute_value'];
            } else {
                $code = $k;
                $value = $v;
            }

            if (!is_array($value)) {
                $value = array($value);
            }
            $existValue = isset($result[$code]) ? $result[$code] : array();
            $result[$code] = array_merge($existValue, $value);
        }

        return $result;
    }

    public function getFrontendInput($attributeCode)
    {
        $attributes = Mage::getModel('amshopby/filter')->getResourceCollection()->addFrontendInput($attributeCode);
        return $attributes->getFirstItem();
    }
        
    public function getOptionsForFilter($attributeCode, $frontendInput)
    {
        $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attributeCode);
        if ($attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);
        }

        $values = array();
        foreach ($options as $option) {
            if ('select' == $frontendInput) {
                $values[$option['value']] = $option['label'];
            } elseif ('multiselect' == $frontendInput) {
                $values[] = array(
                    'value' => $option['value'],
                    'label' => $option['label'],
                );
            }
        } 
        return $values;
    }
}