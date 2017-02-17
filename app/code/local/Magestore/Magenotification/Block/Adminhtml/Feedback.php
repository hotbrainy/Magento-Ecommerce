<?php
class Magestore_Magenotification_Block_Adminhtml_Feedback extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_feedback';
    $this->_blockGroup = 'magenotification';
    $this->_headerText = Mage::helper('magenotification')->__('Feedbacks Manager');
    parent::__construct();
    $this->_updateButton('add', 'label', Mage::helper('magenotification')->__('Post Feedback'));	
  }
}