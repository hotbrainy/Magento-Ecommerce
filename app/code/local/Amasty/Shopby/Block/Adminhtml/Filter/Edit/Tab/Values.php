<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
class Amasty_Shopby_Block_Adminhtml_Filter_Edit_Tab_Values extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('valuesGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('title');
        $this->setDefaultDir('ASC'); 
    }

    protected function _prepareCollection()
    {
        $values = Mage::getResourceModel('amshopby/value_collection')
            ->addFieldToFilter('filter_id', Mage::registry('amshopby_filter')->getId());
        $this->setCollection($values);
        
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $hlp = Mage::helper('amshopby');

        $this->addColumn('option_id', array(
            'header'    => $hlp->__('ID'),
            'index'     => 'option_id',
            'width'     => '50px', 
        ));
       
        $this->addColumn('title', array(
            'header'    => $hlp->__('Title'),
            'index'     => 'title',
            'getter'    => 'getCurrentTitle',
        ));

        $this->addColumn('url_alias', array(
            'header'    => $hlp->__('URL alias'),
            'index'     => 'url_alias',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/amshopby_value/edit', array('id' => $row->getValueId()));
    }

}