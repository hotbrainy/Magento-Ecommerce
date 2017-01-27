<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * @method string getAttributeCode()
 * @method int getDisplayType()
 * @method string getExcludeFrom()
 * @method string getIncludeIn()
 * @method int getSingleChoice()
 * @method int getShowOnList()
 * @method int getSeoNofollow()
 * @method int getSeoNoindex()
 * @method int getHideCounts()
 * @method int getUseAndLogic()
 * @method Amasty_Shopby_Model_Filter setDisplayType(int)
 */
class Amasty_Shopby_Model_Filter extends Mage_Core_Model_Abstract
{
    public function _construct()
    {    
        $this->_init('amshopby/filter');
    }

    public function getDisplayTypeString()
    {
        $hash = $this->getDisplayTypeOptionsSource()->getHash();
        return $hash[$this->getDisplayType()];
    }

    public function getDisplayTypeOptionsSource()
    {
        $sourceName = ($this->getBackendType() == 'decimal') ? 'price' : 'attribute';
        $modelName = 'amshopby/source_' . $sourceName;
        $source = Mage::getModel($modelName);
        return $source;
    }

    public function getIncludeInArray()
    {
        $cats = trim(str_replace(' ', '', $this->getIncludeIn()));
        return ($cats == '') ? null : explode(',', $cats);
    }

    public function getExcludeFromArray()
    {
        $cats = trim(str_replace(' ', '', $this->getExcludeFrom()));
        return ($cats == '') ? array() : explode(',', $cats);
    }

    public function getAttributeId()
    {
        if (!$this->hasData('attribute_id')) {
            /** @var Mage_Catalog_Model_Resource_Attribute $resource */
            $resource = Mage::getResourceModel('catalog/attribute');
            $attributeId = $resource->getIdByCode(Mage_Catalog_Model_Product::ENTITY, $this->getAttributeCode());
            $this->setData('attribute_id', $attributeId);
        }
        $attributeId = $this->getData('attribute_id');
        return $attributeId;
    }
}
