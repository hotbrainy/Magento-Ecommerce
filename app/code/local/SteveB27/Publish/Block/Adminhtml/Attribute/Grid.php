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

class SteveB27_Publish_Block_Adminhtml_Attribute_Grid extends Mage_Eav_Block_Adminhtml_Attribute_Grid_Abstract
{
    protected function _prepareCollection() {
        $collection = Mage::getModel('publish/attribute')->getCollection()
            ->addVisibleFilter();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns() {
        parent::_prepareColumns();
        $this->addColumnAfter('is_global', array(
            'header'=>Mage::helper('publish')->__('Scope'),
            'sortable'=>true,
            'index'=>'is_global',
            'type' => 'options',
            'options' => array(
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE   =>Mage::helper('publish')->__('Store View'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('publish')->__('Website'),
                Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL  =>Mage::helper('publish')->__('Global'),
            ),
            'align' => 'center',
        ), 'is_user_defined');
        return $this;
    }
}