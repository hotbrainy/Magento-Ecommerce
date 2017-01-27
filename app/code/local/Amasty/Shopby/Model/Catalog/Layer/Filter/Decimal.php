<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


class Amasty_Shopby_Model_Catalog_Layer_Filter_Decimal extends Amasty_Shopby_Model_Catalog_Layer_Filter_Decimal_Adapter
{
    private $_rangeSeparator = ',';
    private $_fromToSeparator = '-';

    protected $settings = null;

    public function getItemsCount()
    {
        $cnt = parent::getItemsCount();

        if ($this->calculateRanges()) {
            $hide = Mage::getStoreConfig('amshopby/general/hide_one_value') && $cnt == 1;
        } else {
            $min = $this->getMinValue();
            $max = $this->getMaxValue();
            $hide = $min == $max;
        }

        return $hide ? 0 : $cnt;
    }

    public function getSettings()
    {
        if (is_null($this->settings)){
            $this->settings = Mage::getResourceModel('amshopby/filter')
              ->getFilterByAttributeId($this->getAttributeModel()->getAttributeId()); 
        }
        return $this->settings;
    }
    
    /**
     * Retrieve resource instance
     *
     * @return Amasty_Shopby_Model_Mysql4_Decimal
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = Mage::getModel('amshopby/mysql4_decimal');
        }
        return $this->_resource;
    }

    /**
     * Apply decimal range filter to product collection
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Mage_Catalog_Block_Layer_Filter_Decimal $filterBlock
     * @return Mage_Catalog_Model_Layer_Filter_Decimal
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
        if (!$this->calculateRanges()){
            $this->_items = array($this->_createItem('', 0, 0));
        }         

        $filterBlock->setValueFrom(Mage::helper('amshopby')->__('From'));
        $filterBlock->setValueTo(Mage::helper('amshopby')->__('To'));

        $input = $request->getParam($this->getRequestVar());
        $fromTo = $this->_parseRequestedValue($input);
        if (is_null($fromTo)) {
            return $this;
        }
        list($from, $to) = $fromTo;

        $attributeCode = $this->getAttributeModel()->getAttributeCode();
        /** @var Amasty_Shopby_Helper_Attributes $attributeHelper */
        $attributeHelper = Mage::helper('amshopby/attributes');
        if ($attributeHelper->lockApplyFilter($attributeCode, 'attr')) {
            $this->_getResource()->applyFilterToCollection($this, $from, $to);

            $this->getLayer()->getState()->addFilter(
                $this->_createItem($this->_renderItemLabel($from, $to, true), $input)
            );
        }

        $filterBlock->setValueFrom(($from > 0) ? $from : '');
        $filterBlock->setValueTo(($to > 0) ? $to : '');

        if ($this->hideAfterSelection()){
            $this->_items = array();
        }
        elseif ($this->calculateRanges()){
            $this->_items = array($this->_createItem('', 0, 0));
        }

        if (!$this->calculateRanges()) {
            /** @var Amasty_Shopby_Helper_Layer_Cache $cache */
            $cache = Mage::helper('amshopby/layer_cache');
            $cache->limitLifetime(Amasty_Shopby_Helper_Layer_Cache::LIFETIME_SESSION);
        }

        return $this;
        
    }

    protected function _parseRequestedValue($input)
    {
        if (!$input) {
            return null;
        }

        /* Try $index, $range */
        $inputVals = explode($this->_rangeSeparator, $input);
        if (count($inputVals) == 2) {
            list($index, $range) = $inputVals;
            $from  = ($index-1) * $range;
            $to    = $index * $range;
            return array($from, $to);
        }

        /* Try from to */
        $inputVals = explode($this->_fromToSeparator, $input);
        if (count($inputVals) == 2) {
            list ($from, $to) = $inputVals;
            $from  = floatval($from);
            $to    = floatval($to);
            if ($from < 0.01 && $to < 0.01) {
                return null;
            }
            return array($from, $to);
        }

        return null;
    }

    protected function _renderItemLabel($range, $index, $isFromTo = false)
    {
		if(!$isFromTo) {
			$from  = ($index-1) * $range;
			$to    = $index * $range;
		} else {
			$from = $range;
			$to = $index;
		}
		if(!$from) {
			$minMax = $this->_getResource()->getMinMax($this);
			$from = floatval($minMax[0]);
		}
        if ($to > 0) {
            $result = Mage::helper('catalog')->__('%s - %s', $from, $to);
        } else {
            $result = Mage::helper('catalog')->__('%s and above', $from);
        }

        $settings = $this->getSettings();
        if (!empty($settings['value_label'])) {
            $result.= Mage::helper('catalog')->__(' %s', $settings['value_label']);
        }

        return $result;
    }

    public function addFacetCondition()
    {
        if (!$this->calculateRanges()) {
            return false;
        }

        $code = $this->getAttributeModel()->getAttributeCode();
        $key = 'amshopby_facet_added_' . $code;
        if (Mage::registry($key)) {
            return;
        }

        parent::addFacetCondition();
        Mage::register($key, true);
    }
    
    public function getRange()
    {
        $settings = $this->getSettings();
        if (!empty($settings['range'])){
            return $settings['range'];
        }
            
        return parent::getRange(); 
    }
    
    public function calculateRanges()
    {
        $settings = $this->getSettings();
        return $settings['display_type'] == Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_DEFAULT
        || $settings['display_type'] == Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_DROPDOWN;
    } 
    
    public function hideAfterSelection()
    {
        $settings = $this->getSettings();
        if ($settings['from_to_widget']){
            return false;
        }
        if ($settings['display_type'] == Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_SLIDER){
            return false;
        }
        return true;
    }
}