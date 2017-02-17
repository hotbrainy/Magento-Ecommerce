<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY,"price","used_for_sort_by",0);
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY,"book_series_length","used_for_sort_by",0);
$installer->updateAttribute(Mage_Catalog_Model_Product::ENTITY,"publish_author","used_for_sort_by",1);

$installer->endSetup();