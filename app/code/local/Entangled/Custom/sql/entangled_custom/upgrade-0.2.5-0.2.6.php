<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$attrCode = 'bestseller_rank';
$attrGroupName = 'General';
$attrLabel = 'Best sellers';
$attrNote = '';

$objCatalogEavSetup = Mage::getResourceModel('catalog/eav_mysql4_setup', 'core_setup');
$attrIdTest = $objCatalogEavSetup->getAttributeId(Mage_Catalog_Model_Product::ENTITY, $attrCode);

if ($attrIdTest === false) {
    $objCatalogEavSetup->addAttribute(Mage_Catalog_Model_Product::ENTITY, $attrCode, array(
        'group' => $attrGroupName,
        'sort_order' => 4,
        'type' => 'int',
        'label' => $attrLabel,
        'note' => $attrNote,
        'input' => 'text',
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'visible' => true,
        'searchable' => false,
        'comparable' => false,
        'wysiwyg_enabled' => true,
        'is_html_allowed_on_front' => true,
        'user_defined' => true,
        'default' => '0',
        'visible_on_front' => true,
        'used_in_product_listing'=> true,
        'used_for_sort_by' => true,
        'required' => false,
    ));
}

$installer->endSetup();