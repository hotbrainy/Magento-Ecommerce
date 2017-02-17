<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * @method string getCmsBlockId()
 * @method string getCmsBlockBottomId()
 * @method string getDescr()
 * @method int getFilterId()
 * @method string getImgBig()
 * @method string getImgMedium()
 * @method string getImgSmall()
 * @method string getImgSmallHover()
 * @method string getMetaDescr()
 * @method string getMetaKw()
 * @method string getMetaTitle()
 * @method int getOptionId()
 * @method boolean getShowOnList()
 * @method boolean setShowOnList()
 * @method int getSortOrder()
 * @method string getTitle()
 * @method string getUrlAlias()
 * @method setTitle(string $title)
 */
class Amasty_Shopby_Model_Value extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        $this->_init('amshopby/value');
    }

    public function getCurrentTitle(){
        return $this->_getUnserializedValue("Title");
    }

    public function getCurrentDescr(){
        return $this->_getUnserializedValue("Descr");
    }

    public function getCurrentMetaDescr(){
        return $this->_getUnserializedValue("MetaDescr");
    }

    public function getCurrentMetaKw(){
        return $this->_getUnserializedValue("MetaKw");
    }

    public function getCurrentMetaTitle(){
        return $this->_getUnserializedValue("MetaTitle");
    }

    protected function _getUnserializedValue($field){
        $storeId =  Mage::app()->getStore()->getId();
        $value =  $this->{'get'.$field}();
        $unserialized = @unserialize($value);
        if ( !$unserialized ) return $value;
        !empty($unserialized[$storeId]) ? $return =  $unserialized[$storeId] : $return = $unserialized[0];
        return $return;
    }
}