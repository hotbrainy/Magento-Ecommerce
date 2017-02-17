<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

$table               = $installer->getTable('bannerslider/banner');
$connection          = $installer->getConnection();

$connection->addColumn(
    $table,
    'mobile_image',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 255,
        'comment'  => 'Mobile Image',
        'nullable'  => true
    )
);

$installer->endSetup();