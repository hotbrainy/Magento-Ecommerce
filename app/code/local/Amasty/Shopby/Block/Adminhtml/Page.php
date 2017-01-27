<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * @author Amasty
 */   
class Amasty_Shopby_Block_Adminhtml_Page extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_page';
    $this->_blockGroup = 'amshopby';
    $this->_headerText = Mage::helper('amshopby')->__('Pages');
    $this->_addButtonLabel = Mage::helper('amshopby')->__('Add Page');
    parent::__construct();
  }
}


