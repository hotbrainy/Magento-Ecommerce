<?php

class Entangled_Reports_Block_Adminhtml_Books extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'entangled_reports';
        $this->_controller = 'adminhtml_books';
        $this->_headerText = Mage::helper('entangled_reports')->__('Books Sales');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/sales', array('_current' => true));
    }
}
