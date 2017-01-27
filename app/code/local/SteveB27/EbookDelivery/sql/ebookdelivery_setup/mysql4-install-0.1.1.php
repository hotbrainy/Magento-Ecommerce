<?php

$installer = $this;
 
$installer->startSetup();
 
$installer->run("
DROP TABLE IF EXISTS {$installer->getTable('ebookdelivery/devices')};
CREATE TABLE {$installer->getTable('ebookdelivery/devices')} (
 `id` int(11) unsigned NOT NULL auto_increment,
  `customer_id` int(10),
  `device_type` varchar(64),
  `device_email` varchar(100),
  `device_nickname` varchar(100),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
