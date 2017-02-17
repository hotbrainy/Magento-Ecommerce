<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * @author Amasty
 */  
class Amasty_Shopby_Block_Adminhtml_Page_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('pageGrid');
      $this->setDefaultSort('page_id');
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('amshopby/page')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
    $hlp =  Mage::helper('amshopby'); 
    $this->addColumn('page_id', array(
      'header'    => $hlp->__('ID'),
      'align'     => 'right',
      'width'     => '50px',
      'index'     => 'page_id',
    ));
    
    $this->addColumn('num', array(
      'header'    => $hlp->__('Number of Selections'),
      'align'     => 'right',
      'width'     => '50px',
      'index'     => 'num',
    ));

    $this->addColumn('meta_title', array(
        'header'    => $hlp->__('Page Title'),
        'index'     => 'meta_title',
    ));
    
    $this->addColumn('meta_descr', array(
        'header'    => $hlp->__('Meta Description'),
        'index'     => 'meta_descr',
    ));


    return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
  
  protected function _prepareMassaction()
  {
    $this->setMassactionIdField('page_id');
    $this->getMassactionBlock()->setFormFieldName('pages');
    
    $this->getMassactionBlock()->addItem('delete', array(
         'label'    => Mage::helper('amshopby')->__('Delete'),
         'url'      => $this->getUrl('*/*/massDelete'),
         'confirm'  => Mage::helper('amshopby')->__('Are you sure?')
    ));
    
    return $this; 
  }

}