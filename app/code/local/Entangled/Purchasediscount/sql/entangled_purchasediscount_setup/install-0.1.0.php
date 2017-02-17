<?php
$installer = $this;
$installer->startSetup();

$installer->run("
    CREATE TABLE `{$installer->getTable('entangled_purchasediscount/purchasedate')}` (
      `purchase_id` int(11) NOT NULL auto_increment,
      `customer_id` VARCHAR(64) NOT NULL,
      `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
      PRIMARY KEY  (`purchase_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();