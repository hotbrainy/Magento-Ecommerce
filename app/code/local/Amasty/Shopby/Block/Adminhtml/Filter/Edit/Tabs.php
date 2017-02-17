<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
class Amasty_Shopby_Block_Adminhtml_Filter_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('filterTabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('amshopby')->__('Filter Properties'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('amshopby')->__('General'),
            'content'   => $this->getLayout()->createBlock('amshopby/adminhtml_filter_edit_tab_general')->toHtml(),
        ));
        
        /*
         * Add options tab only for decimals
         */         
        if (Mage::registry('amshopby_filter')->getBackendType() != 'decimal') {
            $this->addTab('values', array(
                'label'     => Mage::helper('amshopby')->__('Options'),
                'class'     => 'ajax',
                'url'       => $this->getUrl('*/*/values', array('_current' => true)),
            ));
        }

        $this->_updateActiveTab();
        return parent::_beforeToHtml();
    }
    
    protected function _updateActiveTab()
    {
        $tabId = $this->getRequest()->getParam('tab');
        if ($tabId) {
            $tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
            if ($tabId) {
                $this->setActiveTab($tabId);
            }
        }
        else {
           $this->setActiveTab('general'); 
        }
    }
}