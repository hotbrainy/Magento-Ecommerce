<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('magenotification_license')};

CREATE TABLE {$this->getTable('magenotification_license')} (
  `license_id` int(11) unsigned NOT NULL auto_increment,
  `extension_code` varchar(100) NOT NULL default '',
  `license_key` text NOT NULL default '',
  `active_at` date NOT NULL,
  `sum_code` varchar(255),
  `response_code` smallint(5),
  `domains` varchar(255),
  `is_valid` tinyint(1),
  PRIMARY KEY (`license_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 