<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('magenotification_log')};

CREATE TABLE {$this->getTable('magenotification_log')} (
  `log_id` int(11) unsigned NOT NULL auto_increment,
  `extension_code` varchar(100) NOT NULL default '',
  `license_type` varchar(50) NOT NULL default '',
  `license_key` text NOT NULL default '',
  `check_date` date NOT NULL,
  `sum_code` varchar(255),
  `response_code` smallint(5),
  `expired_time` varchar(255),
  `is_valid` tinyint(1),
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 