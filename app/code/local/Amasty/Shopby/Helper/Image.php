<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */ 
class Amasty_Shopby_Helper_Image extends Mage_Catalog_Helper_Image
{
    protected $requestConfigurableMap;

    public function setProduct($product)
    {
        if (!isset($this->requestConfigurableMap)) {
            $this->computeRequestConfigurableMap();
        }

        if ($this->requestConfigurableMap && $product->isConfigurable() && $product->isSaleable()) {
            $child = $this->getMatchingSimpleProduct($product);
            if (is_object($child)) {
                $product = $child;
            }
        }
        parent::setProduct($product);
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Model_Product|null
     */
    protected function getMatchingSimpleProduct($product)
    {
        /** @var Mage_Catalog_Model_Product_Type_Configurable $productTypeIns */
        $productTypeIns = $product->getTypeInstance(true);
        $children = $productTypeIns->getUsedProductCollection($product);
        foreach ($this->requestConfigurableMap as $code => $values) {
            $children->addAttributeToFilter($code, array('in' => $values));
        }

        $ids = $children->getAllIds();
        $resultChild = null;
        foreach ($ids as $id) {
            $child = Mage::getModel('catalog/product')->load($id);
            if ($child->getThumbnail() != 'no_selection') {
                $resultChild = $child;
            }
        }

        return $resultChild;
    }

    protected function computeRequestConfigurableMap()
    {
        $this->requestConfigurableMap = array();
        $configurableCodes = explode(",", trim(Mage::getStoreConfig('amshopby/general/configurable_images')));
        $requestParams = Mage::app()->getRequest()->getQuery();
        $inRequestConfigurableCodes = array_intersect($configurableCodes, array_keys($requestParams));

        foreach ($inRequestConfigurableCodes as $code) {
            $value = $requestParams[$code];
            if (strpos($value, ",") !== false) {
                $values = explode(",", $value);
            } else {
                $values = array($value);
            }

            $this->requestConfigurableMap[$code] = $values;
        }
    }
}
