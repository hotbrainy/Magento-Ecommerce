<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
class Amasty_Shopby_Block_List extends Mage_Core_Block_Template
{
    private $items = array();
    
    protected function _prepareLayout()
    {

        $entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
        /** @var Mage_Eav_Model_Attribute $attribute */
        $attribute  = Mage::getModel('catalog/resource_eav_attribute')
                ->loadByCode($entityTypeId, $this->getAttributeCode());
        
        if (!$attribute->getId()){
            return parent::_prepareLayout();
        }
          
        $options = $attribute->getFrontend()->getSelectOptions();
        array_shift($options);
        
        $filter = new Varien_Object();
        // important when used at category pages
        $layer = Mage::getModel('catalog/layer')
            ->setCurrentCategory(Mage::app()->getStore()->getRootCategoryId());
        
        $filter->setLayer($layer);
        $filter->setStoreId(Mage::app()->getStore()->getId());
        $filter->setAttributeModel($attribute);
        
        $optionsCount = Mage::getResourceModel('catalog/layer_filter_attribute')->getCount($filter);

        usort($options, array($this, '_sortByName'));

        $images = Mage::getStoreConfig('amshopby/brands/show_images') ? $this->_getOptionImages($options) : null;

        /** @var Amasty_Shopby_Helper_Url $urlHelper */
        $urlHelper = Mage::helper('amshopby/url');

        $c = 0;
        $letters = array();
        foreach ($options as $opt){
            if (!empty($optionsCount[$opt['value']])){
                $opt['cnt'] = $optionsCount[$opt['value']];
                $opt['url'] = $urlHelper->getOptionUrl($attribute->getAttributeCode(), $opt['value']);
                $opt['img'] = $images ? $images[$opt['value']] : null;

                if (function_exists('mb_strtoupper')) {
                    $i = mb_strtoupper(mb_substr($opt['label'], 0, 1, 'UTF-8'));
                } else {
                    $i = strtoupper(substr($opt['label'], 0, 1));
                }

if (is_numeric($i)) { $i = '#'; }
                
                if (!isset($letters[$i]['items'])){
                    $letters[$i]['items'] = array();
                }
                    
                $letters[$i]['items'][] = $opt;
               
                if (!isset($letters[$i]['count'])){
                    $letters[$i]['count'] = 0;
                }
                    
                $letters[$i]['count']++;
                
                ++$c;
            }
        }
        
        if (!$letters){
            return parent::_prepareLayout();
        }
        
        $itemsPerColumn = ceil(($c + sizeof($letters)) / max(1, abs(intVal($this->getColumns()))));

        $col = 0; // current column 
        $num = 0; // current number of items in column
        foreach ($letters as $letter => $items){
            $this->items[$col][$letter] = $items['items'];
            $num += $items['count'];
            $num++;
            if ($num >= $itemsPerColumn){
                $num = 0;
                $col++;
            }
        }
        
        return parent::_prepareLayout();
    }

    protected function _getOptionImages($options)
    {
        $ids = array();
        foreach ($options as $opt){
            $ids[] = $opt['value'];
        }
        $collection = Mage::getResourceModel('amshopby/value_collection')
            ->addFieldToFilter('option_id', array('in'=>$ids))
            ->load();
        $images = array();
        foreach ($collection as $value){
            $images[$value->getOptionId()] = $value->getImgBig() ? Mage::getBaseUrl('media') . 'amshopby/' . $value->getImgBig() : null;
        }
        return $images;
    }
    
    public function getItems()
    {
        return $this->items;
    }
    
    public function _sortByName($a, $b)
    {
        $a['label'] = trim($a['label']);
        $b['label'] = trim($b['label']);

        if ($a == '') return 1;
        if ($b == '') return -1;

        $x = substr($a['label'], 0, 1);
        $y = substr($b['label'], 0, 1);
        if (is_numeric($x) && !is_numeric($y)) return 1;
        if (!is_numeric($x) && is_numeric($y)) return -1;

        return strcmp(strtoupper($a['label']), strtoupper($b['label']));
    }

}