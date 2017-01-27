<?php
/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->changeColumn($installer->getTable('entangled_returns/request'),"product_sku","product_sku","VARCHAR(255) NOT NULL COMMENT 'Product Sku'");

$installer->endSetup();