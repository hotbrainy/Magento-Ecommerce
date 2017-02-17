<?php
/**
 * Custom Publisher Models
 * 
 * Add custom model types, such as author, which can be used as a product
 * attribute while proviting additional details.
 * 
 * @license 	http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 * @copyright	Steven Brown March 12, 2016
 * @author		Steven Brown <steveb.27@outlook.com>
 */

class SteveB27_Publish_Block_Adminhtml_Attribute_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct() {
        parent::__construct();
        $this->setId('attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('publish')->__('Attribute Information'));
    }
    
    protected function _beforeToHtml() {
        $this->addTab('main', array(
            'label'     => Mage::helper('publish')->__('Properties'),
            'title'     => Mage::helper('publish')->__('Properties'),
            'content'   => $this->getLayout()->createBlock('publish/adminhtml_attribute_edit_tab_main')->toHtml(),
            'active'    => true
        ));
        $this->addTab('labels', array(
            'label'     => Mage::helper('publish')->__('Manage Label / Options'),
            'title'     => Mage::helper('publish')->__('Manage Label / Options'),
            'content'   => $this->getLayout()->createBlock('publish/adminhtml_attribute_edit_tab_options')->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
}