<?php
$installer = $this;
$installer->startSetup();

$tables = array(
	Mage::getModel('core/resource')->getTableName('publish_author_char'),
	Mage::getModel('core/resource')->getTableName('publish_author_datetime'),
	Mage::getModel('core/resource')->getTableName('publish_author_decimal'),
	Mage::getModel('core/resource')->getTableName('publish_author_file'),
	Mage::getModel('core/resource')->getTableName('publish_author_int'),
	Mage::getModel('core/resource')->getTableName('publish_author_text'),
	Mage::getModel('core/resource')->getTableName('publish_author_varchar'),
);

foreach($tables as $table) {
	$connection = $installer->getConnection();
	$connection->addIndex(
		$table,
		$installer->getIdxName(
			$table,
			array(
				'attribute_id',
				'store_id',
				'entity_id',
			),
			Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
		),
		array(
			'attribute_id',
			'store_id',
			'entity_id',
		),
		Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
	);
}

$installer->endSetup();