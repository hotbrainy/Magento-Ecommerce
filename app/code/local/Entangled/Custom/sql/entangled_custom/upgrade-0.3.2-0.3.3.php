<?php

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY,"category_genre");
$installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY,"category_imprint");

$installer->endSetup();