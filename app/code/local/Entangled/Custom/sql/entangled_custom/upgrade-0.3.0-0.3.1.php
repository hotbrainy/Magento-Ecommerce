<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY,"bestseller_rank","is_visible_on_front",0);
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY,"book_series","is_visible_on_front",1);
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY,"book_series_2","is_visible_on_front",1);

$installer->endSetup();