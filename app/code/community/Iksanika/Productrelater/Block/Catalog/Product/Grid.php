<?php

/**
 * Iksanika llc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.iksanika.com/products/IKS-LICENSE.txt
 *
 * @category   Iksanika
 * @package    Iksanika_Productrelater
 * @copyright  Copyright (c) 2013 Iksanika llc. (http://www.iksanika.com)
 * @license    http://www.iksanika.com/products/IKS-LICENSE.txt
 */

class Iksanika_Productrelater_Block_Catalog_Product_Grid extends Iksanika_Productrelater_Block_Widget_Grid
{
    protected static $columnType = array(
        'id'                    =>  array('type'=>'number'),
        'product'               =>  array('type'=>'checkbox'),
        'name'                  =>  array('type'=>'text'),
        'type_id'               =>  array('type'=>'text'),
        'attribute_set_id'      =>  array('type'=>'text'),
        'sku'                   =>  array('type'=>'text'),
        'price'                 =>  array('type'=>'text'),
        'qty'                   =>  array('type'=>'text'),
        'is_in_stock'           =>  array('type'=>'text'),
        'visibility'            =>  array('type'=>'text'),
        'status'                =>  array('type'=>'text'),
        'websites'              =>  array('type'=>'text'),

        'related_ids'           =>  array('type'=>'input'),
        'cross_sell_ids'        =>  array('type'=>'input'),
        'up_sell_ids'           =>  array('type'=>'input'),
    );
    
    
    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->prepareDefaults();
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setTemplate('iksanika/productrelater/catalog/product/grid.phtml');
        $this->setMassactionBlockName('productrelater/widget_grid_massaction');
    }
    
    private function prepareDefaults() 
    {
        $this->setDefaultLimit(20);
        $this->setDefaultPage(1);
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {
        
        $collection = $this->getCollection();
        $collection = !$collection ? Mage::getModel('catalog/product')->getCollection() : $collection;

        $store = $this->_getStore();
        $collection
            ->joinField(
                'qty', 
                'cataloginventory/stock_item', 
                'qty', 
                'product_id=entity_id', 
                '{{table}}.stock_id=1', 
                'left')
            ->joinField(
                'related_ids',
                'catalog/product_link',
                'linked_product_id',
                'product_id=entity_id',
                '{{table}}.link_type_id=1', // 1- relation, 4 - up_sell, 5 - cross_sell
                'left')
            ->joinField(
                'cross_sell_ids',
                'catalog/product_link',
                'linked_product_id',
                'product_id=entity_id',
                '{{table}}.link_type_id=5', // 1- relation, 4 - up_sell, 5 - cross_sell
                'left')
            ->joinField(
                'up_sell_ids',
                'catalog/product_link',
                'linked_product_id',
                'product_id=entity_id',
                '{{table}}.link_type_id=4', // 1- relation, 4 - up_sell, 5 - cross_sell
                'left');

        $collection->groupByAttribute('entity_id');

        if ($store->getId())
        {
            //$collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        }
        else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }
        
        foreach(self::$columnType as $col => $true) 
        {
            if($col == 'related_ids' || $col == 'cross_sell_ids' || $col == 'up_sell_ids')
            { 
                $filter = $this->getParam($this->getVarNameFilter());
                if($filter)
                {
                    $filter_data = Mage::helper('adminhtml')->prepareFilterString($filter);
                    if(isset($filter_data[$col]))
                    {
                        if(trim($filter_data[$col])=='')
                            continue;
                        $relatedIds = explode(',', $filter_data[$col]);
                        $relatedIdsArray = array();
                        foreach($relatedIds as $relatedId)
                        {
                            //$collection->addCategoryFilter(Mage::getModel('catalog/category')->load($categoryId));
                            $relatedIdsArray[] = intval($relatedId);
                        }
                        $collection->addAttributeToFilter($col, array( 'in' => $relatedIdsArray));                        
                    }
                }
            }
            if($col == 'qty' || $col == 'websites' || $col=='id'|| $col=='related_ids'|| $col=='cross_sell_ids'|| $col=='up_sell_ids') 
                continue;
            else
                $collection->addAttributeToSelect($col);
        }

        $this->setCollection($collection);
        
        parent::_prepareCollection();

        $collection->addWebsiteNamesToResult();

        return $this;
    }


    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites',
                    'catalog/product_website',
                    'website_id',
                    'product_id=entity_id',
                    null,
                    'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    public function _applyMyFilter($column)
    {
        // empty filter condition to avoid standard magento conditions
    }

    protected function _prepareColumns()
    {
        $store = $this->_getStore();
        
        $this->addColumn('id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));
        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'name' => 'pu_name[]',
                'index' => 'name'/*,
                'width' => '150px'*/
        ));
        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn('custom_name',
                array(
                    'header'=> Mage::helper('catalog')->__('Name In %s', $store->getName()),
                    'index' => 'custom_name',
                    'width' => '150px'
            ));
        }
        /*
        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '60px',
                'index' => 'type_id',
                'type' => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));
         */
        /*
        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '100px',
                'index' => 'attribute_set_id',
                'type' => 'options',
                'options' => $sets,
        ));
         */
        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
                'name' => 'pu_sku[]',
        ));
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Price'),
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
                'index' => 'price',
                'name' => 'pu_price[]',
        ));
        $this->addColumn('qty',
            array(
                'header'=> Mage::helper('catalog')->__('Qty'),
                'width' => '100px',
                'type'  => 'number',
                'index' => 'qty',
                'name' => 'pu_qty[]',
        ));
        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '70px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));
        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '70px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites',
                array(
                    'header'=> Mage::helper('catalog')->__('Websites'),
                    'width' => '100px',
                    'sortable'  => false,
                    'index'     => 'websites',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }

        $this->addColumn('related_ids',
            array(
                'type'=>'input',
                'index' => 'related_ids',
                'width'=>'80px',
                'filter_condition_callback' => array($this, '_applyMyFilter'),
                'header'=> Mage::helper('catalog')->__('Related IDs'),
        ));
        $this->addColumn('cross_sell_ids',
            array(
                'type'=>'input',
                'index' => 'cross_sell_ids',
                'width'=>'80px',
                'filter_condition_callback' => array($this, '_applyMyFilter'),
                'header'=> Mage::helper('catalog')->__('Cross-Sell IDs'),
        ));
        $this->addColumn('up_sell_ids',
            array(
                'type'=>'input',
                'index' => 'up_sell_ids',
                'width'=>'80px',
                'filter_condition_callback' => array($this, '_applyMyFilter'),
                'header'=> Mage::helper('catalog')->__('Up-Sell IDs'),
        ));
        
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getId',
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'id' => "editlink",
                        'url'     => array(
                            'base'=>'adminhtml/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
        ));

        $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));

        $this->setDestElementId('edit_form');
        
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('product');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'=> Mage::helper('catalog')->__('Delete'),
             'url'  => $this->getUrl('*/*/massDelete'),
             'confirm' => Mage::helper('catalog')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current' => true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('catalog')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        
        $this->getMassactionBlock()->addItem('attributes', 
            array(
                'label' => Mage::helper('catalog')->__('Update attributes'),
                'url'   => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current'=>true))
                )
        );

        $this->getMassactionBlock()->addItem('otherDivider', $this->getDivider("Other"));
        
        /*
         * Prepare list of column for update
         */
        $this->getMassactionBlock()->addItem('save', 
            array(
                'label' => Mage::helper('catalog')->__('Update'),
                'url'   => $this->getUrl('*/*/massUpdateProducts', array('_current'=>true)),
                'fields' => array(0=>'product', 1=>'related_ids', 2=> 'cross_sell_ids', 3=> 'up_sell_ids')
            )
        );
        
        
        $this->getMassactionBlock()->addItem('relatedDivider', $this->getCleanDivider());

        $this->getMassactionBlock()->addItem('otherDividerSalesMotivation', $this->getDivider("Product Relator"));
        
        $this->getMassactionBlock()->addItem('relatedEachOther', array(
            'label' => $this->__('Related: To Each Other'),
            'url'   => $this->getUrl('*/*/massRelatedEachOther', array('_current'=>true)),
            'callback' => 'specifyRelatedEachOther()',
        ));
        $this->getMassactionBlock()->addItem('relatedTo', array(
            'label' => $this->__('Related: Add ..'),
            'url'   => $this->getUrl('*/*/massRelatedTo', array('_current'=>true)),
            'callback' => 'specifyRelatedProducts()'
        ));
        $this->getMassactionBlock()->addItem('relatedClean', array(
            'label' => $this->__('Related: Clear'),
            'url'   => $this->getUrl('*/*/massRelatedClean', array('_current'=>true)),
            'callback' => 'specifyRelatedClean()'
        ));
        
        
        $this->getMassactionBlock()->addItem('crossSellDivider', $this->getCleanDivider());

        $this->getMassactionBlock()->addItem('crossSellEachOther', array(
            'label' => $this->__('Cross-Sell: To Each Other'),
            'url'   => $this->getUrl('*/*/massCrossSellEachOther', array('_current'=>true)),
            'callback' => 'specifyCrossSellEachOther()',
        ));
        $this->getMassactionBlock()->addItem('crossSellTo', array(
            'label' => $this->__('Cross-Sell: Add ..'),
            'url'   => $this->getUrl('*/*/massCrossSellTo', array('_current'=>true)),
                'callback' => 'chooseWhatToCrossSellTo()'
        ));
        $this->getMassactionBlock()->addItem('crossSellClear', array(
            'label' => $this->__('Cross-Sell: Clear'),
            'url'   => $this->getUrl('*/*/massCrossSellClear', array('_current'=>true)),
            'callback' => 'specifyCrossSellClean()',
        ));
        
        
        $this->getMassactionBlock()->addItem('upSellDivider', $this->getCleanDivider());
            
        $this->getMassactionBlock()->addItem('upSellTo', array(
            'label' => $this->__('Up-Sells: Add ..'),
            'url'   => $this->getUrl('*/*/massUpSellTo', array('_current'=>true)),
                'callback' => 'chooseWhatToUpSellTo()'
        ));
        $this->getMassactionBlock()->addItem('upSellClear', array(
            'label' => $this->__('Up-Sells: Clear'),
            'url'   => $this->getUrl('*/*/massUpSellClear', array('_current'=>true)),
            'callback' => 'specifyUpSellClean()',
        ));
        
        return $this;
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    
    protected function getDivider($divider="*******") {
        $dividerTemplate = array(
          'label' => '********'.$this->__($divider).'********',
          'url'   => $this->getUrl('*/*/index', array('_current'=>true)),
          'callback' => "null"
        );
        return $dividerTemplate;
    }

    protected function getSubDivider($divider="-------") {
        $dividerTemplate = array(
          'label' => '--------'.$this->__($divider).'--------',
          'url'   => $this->getUrl('*/*/index', array('_current'=>true)),
          'callback' => "null"
        );
        return $dividerTemplate;
    }

    protected function getCleanDivider() {
        $dividerTemplate = array(
          'label' => ' ',
          'url'   => $this->getUrl('*/*/index', array('_current'=>true)),
          'callback' => "null"
        );
        return $dividerTemplate;
    }
    
    
}