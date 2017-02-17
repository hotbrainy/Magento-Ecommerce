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

class SteveB27_Publish_Block_Adminhtml_Author_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct() {
        parent::__construct();
        $this->setId('publish_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('publish')->__('Author Information'));
    }
    
    protected function _prepareLayout(){
        $author = $this->getAuthor();
        $entity = Mage::getModel('eav/entity_type')->load(SteveB27_Publish_Model_Author::ENTITY, 'entity_type_code');
        $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($entity->getEntityTypeId());
        $attributes->addFieldToFilter('attribute_code', array('nin'=>array('meta_title', 'meta_description', 'meta_keywords')));
        $attributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab('info', array(
            'label'     => Mage::helper('publish')->__('Author Information'),
            'title'     => Mage::helper('publish')->__('Author Information'),
            'content'   => $this->getLayout()->createBlock('publish/adminhtml_author_edit_tab_attributes')
                            ->setAttributes($attributes)
                            ->toHtml(),
        ));
        $seoAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->setEntityTypeFilter($entity->getEntityTypeId())
                ->addFieldToFilter('attribute_code', array('in'=>array('meta_title', 'meta_description', 'meta_keywords')));
        $seoAttributes->getSelect()->order('additional_table.position', 'ASC');

        $this->addTab('meta', array(
            'label'     => Mage::helper('publish')->__('Meta'),
            'title'     => Mage::helper('publish')->__('Meta'),
            'content'   => $this->getLayout()->createBlock('publish/adminhtml_author_edit_tab_attributes')
                            ->setAttributes($seoAttributes)
                            ->toHtml(),
        ));
        
        return parent::_beforeToHtml();
    }
    
    public function getAuthor(){
        return Mage::registry('current_author');
    }
}