<?php
$installer = $this;

$installer->startSetup();

$entityTypeId = $installer->getEntityTypeId('catalog_product');
$installer->updateAttribute($entityTypeId,'book_series_2',array(
	'backend_type' 		=> Varien_Db_Ddl_Table::TYPE_DECIMAL,
));

$installer->endSetup();
