<?php

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$table               = $installer->getTable('newsletter/subscriber');
$connection          = $installer->getConnection();

$connection->addColumn(
    $table,
    'author_ids',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
        'comment'  => 'author_ids',
        'nullable'  => true
    )
);

$installer->endSetup();