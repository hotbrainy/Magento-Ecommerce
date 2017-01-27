<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
class Amasty_Shopby_Model_Catalog_Layer_Filter_Price extends Amasty_Shopby_Model_Catalog_Layer_Filter_Price_Adapter
{
    /**
     * Display Types
     */
    const DT_DEFAULT = 0;
    const DT_DROPDOWN = 1;
    const DT_FROMTO = 2;
    const DT_SLIDER = 3;

    public function _srt($a, $b)
    {
        $res = ($a['pos'] < $b['pos']) ? -1 : 1;
        return $res;
    }

    protected function _getCustomRanges()
    {
        $ranges = array();
        $collection = Mage::getModel('amshopby/range')->getCollection()
            ->setOrder('price_frm', 'asc')
            ->load();

        $rate = Mage::app()->getStore()->getCurrentCurrencyRate();
        foreach ($collection as $range) {
            $from = $range->getPriceFrm() * $rate;
            $to = $range->getPriceTo() * $rate;

            $ranges[$range->getId()] = array($from, $to);
        }

        if (!$ranges) {
            echo "Please set up Custom Ranges in the Admin > Catalog > Improved Navigation > Ranges";
            exit;
        }

        return $ranges;
    }

    public function calculateRanges()
    {
        return (Mage::getStoreConfig('amshopby/general/price_type') == self::DT_DEFAULT 
            || Mage::getStoreConfig('amshopby/general/price_type') == self::DT_DROPDOWN);     
    }
    
    public function hideAfterSelection()
    {
        if (Mage::getStoreConfig('amshopby/general/price_from_to')){
            return false;
        }
        if (Mage::getStoreConfig('amshopby/general/price_type') == self::DT_SLIDER){
            return false;
        }
        return true;
    }

    public function getItemsCount()
    {
        $cnt = parent::getItemsCount();

        if ($this->calculateRanges()) {
            $hide = Mage::getStoreConfig('amshopby/general/hide_one_value') && $cnt == 1;
        } else {
            $min = $this->getMinValue();
            $max = $this->getMaxValue();
            $hide = $min == $max;
            $cnt = 1;
        }

        return $hide ? 0 : $cnt;
    }

    public function addFacetCondition()
    {
        if (!$this->calculateRanges()) {
            return;
        }

        if (Mage::registry('amshopby_facet_added_price')) {
            $this->_facets = Mage::registry('amshopby_facet_added_price');
        } else {
            parent::addFacetCondition();
            Mage::register('amshopby_facet_added_price', $this->_facets);
        }
    }
}