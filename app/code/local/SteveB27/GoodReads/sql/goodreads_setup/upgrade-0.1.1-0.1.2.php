<?php
/** @var SteveB27_GoodReads_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY,"sku","used_in_product_listing",1);

$installer->endSetup();