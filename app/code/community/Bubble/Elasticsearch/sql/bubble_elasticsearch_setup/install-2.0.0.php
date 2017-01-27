<?php
/**
 * Adds search_weight column to catalog_eav_attribute table
 *
 * @var $installer Mage_Catalog_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();

try {
    $installer->getConnection()->addColumn(
        $installer->getTable('catalog/eav_attribute'),
        'search_weight',
        "tinyint(1) unsigned NOT NULL DEFAULT '1' after `is_searchable`"
    );
} catch (Exception $e) {
    // Ignore
    Mage::logException($e);
}

// Retrieve product name attribute id in order to boost it by default
$attrId = $installer->getAttributeId('catalog_product', 'name');
if ($attrId) {
    $installer->run("UPDATE `{$installer->getTable('catalog/eav_attribute')}` SET `search_weight` = 5 WHERE `attribute_id` = {$attrId}");
}

$installer->endSetup();