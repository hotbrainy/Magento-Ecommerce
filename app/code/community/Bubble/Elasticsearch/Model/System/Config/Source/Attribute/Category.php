<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Model_System_Config_Source_Attribute_Category
{
    /**
     * Return list of searchable attributes
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        $entityType = Mage::getSingleton('eav/config')->getEntityType('catalog_category');

        /* @var Mage_Catalog_Model_Resource_Category_Attribute_Collection $collection */
        $collection = Mage::getResourceModel('catalog/category_attribute_collection')
            ->setEntityTypeFilter($entityType->getEntityTypeId())
            ->addFieldToFilter('source_model', array(
                array('neq' => 'eav/entity_attribute_source_boolean'),
                array('null' => true)
            ))
            ->addFieldToFilter(
                array('frontend_input', 'is_searchable'),
                array(array('in' => array('text', 'textarea')), '1')
            )
            ->addFieldToFilter('backend_type', array('nin' => array('static', 'decimal')))
            ->addFieldToFilter('attribute_code', array('neq' => 'custom_layout_update'))
            ->setOrder('frontend_label', 'ASC');

        Mage::dispatchEvent('bubble_elasticsearch_category_attributes', array(
            'collection' => $collection,
        ));

        foreach ($collection as $attribute) {
            /** @var Mage_Eav_Model_Entity_Attribute $attribute */
            if ($attribute->getFrontendLabel()) {
                $options[] = array(
                    'value' => $attribute->getAttributeCode(),
                    'label' => $attribute->getFrontendLabel(),
                );
            }
        }

        return $options;
    }
}
