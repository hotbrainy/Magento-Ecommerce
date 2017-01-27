<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
class Amasty_Shopby_Block_Catalog_Layer_Filter_Stock extends Mage_Catalog_Block_Layer_Filter_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('amasty/amshopby/attribute.phtml');
        $this->_filterModelName = 'amshopby/catalog_layer_filter_stock';
    }
    
    /**
     * Get Sort Order for filter
     * @return number
     */
    public function getPosition()
    {
        if (count($this->getItemsAsArray()) == 0) {
            return -1;
        }

    	return parent::getPosition();
    }
    
    public function getDisplayType()
    {
    	return 0;
    }
    
	public function getItemsAsArray()
    {
        $items = array(); 
        foreach (parent::getItems() as $itemObject){
        	
            $item = array();
            $item['url']   = $this->htmlEscape($itemObject->getUrl());
            $item['label'] = $itemObject->getLabel();
            $item['count'] = '';
         	$item['countValue']  = $itemObject->getCount();

            /** @todo Fix item counts */
            if (!$this->getHideCounts()) {
                $item['count']  = ' (' . $itemObject->getCount() . ')';
            }


            $item['css'] = 'amshopby-attr';
            if (in_array($this->getDisplayType(), array(1,3))) //dropdown and images
                $item['css'] = '';

            if ($itemObject->getOptionId() == $this->getRequestValue()){
                $item['css'] .= '-selected';
                if (3 == $this->getDisplayType()) //dropdown
                    $item['css'] = 'selected';
            }

            $item['rel'] = $this->getSeoRel() ? ' rel="nofollow" ' : '';

            if ($item['countValue']) {
                $items[] = $item;
            }
        }
        
        return $items;
    }
    
	public function getRequestValue()
    {
        return Mage::app()->getRequest()->getParam('stock');
    }

    protected function _prepareFilter()
    {
        parent::_prepareFilter();
        if (Mage::helper('amshopby')->useSolr()) {
            $this->addFacetCondition();
        }
    }

    public function addFacetCondition()
    {
        $this->_filter->addFacetCondition();
        return $this;
    }
}
