<?php
/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;

$installer->startSetup();

$table               = $installer->getTable('entangled_returns/request');
$connection          = $installer->getConnection();

$connection->addColumn(
    $table,
    'reason_id',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'comment'  => 'reason_id',
        'nullable'  => true
    )
);

$installer->endSetup();