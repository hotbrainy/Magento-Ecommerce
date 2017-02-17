<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * @author Amasty
 */  
class Amasty_Shopby_Block_Adminhtml_Range_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('rangeGrid');
      $this->setDefaultSort('range_id');
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('amshopby/range')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
    $hlp =  Mage::helper('amshopby'); 
    $this->addColumn('range_id', array(
      'header'    => $hlp->__('ID'),
      'align'     => 'right',
      'width'     => '50px',
      'index'     => 'range_id',
    ));
    
    $this->addColumn('price_frm', array(
        'header'    => $hlp->__('From'),
        'index'     => 'price_frm',
    ));
    
    $this->addColumn('price_to', array(
        'header'    => $hlp->__('To'),
        'index'     => 'price_to',
    ));


    return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
  
  protected function _prepareMassaction()
  {
    $this->setMassactionIdField('range_id');
    $this->getMassactionBlock()->setFormFieldName('ranges');
    
    $this->getMassactionBlock()->addItem('delete', array(
         'label'    => Mage::helper('amshopby')->__('Delete'),
         'url'      => $this->getUrl('*/*/massDelete'),
         'confirm'  => Mage::helper('amshopby')->__('Are you sure?')
    ));
    
    return $this; 
  }

}