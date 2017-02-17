<?php

$installer = $this;
/* @var $installer Mage_Sales_Model_Mysql4_Setup */

$installer->startSetup();

$installer->setConfigData("catalog/frontend/grid_per_page_values",20);
$installer->setConfigData("catalog/frontend/grid_per_page",20);
$installer->setConfigData("catalog/frontend/list_per_page",20);
$installer->setConfigData("catalog/frontend/list_per_page_values",20);

$installer->endSetup();
