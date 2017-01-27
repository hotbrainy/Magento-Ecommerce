<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('magenotification_extension_feedbackmessage')};
DROP TABLE IF EXISTS {$this->getTable('magenotification_extension_feedback')};
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


CREATE TABLE {$this->getTable('magenotification_extension_feedback')} (
  `feedback_id` int(11) unsigned NOT NULL auto_increment,
  `code` varchar(255) NOT NULL default '',
  `extension` varchar(255) NOT NULL default '',
  `extension_version` varchar(50) NOT NULL default '',
  `coupon_code` varchar(255) NOT NULL default '',
  `coupon_value` varchar(50) NOT NULL default '',
  `expired_counpon` datetime NOT NULL,
  `content` text NOT NULL default '',
  `file` text NOT NULL default '',
  `comment` text NOT NULL default '',
  `latest_message` text NOT NULL default '',  
  `latest_response` text NOT NULL default '',
  `latest_response_time` datetime,
  `status` tinyint(1) NOT NULL DEFAULT '3',
  `is_sent` tinyint(1) NOT NULL DEFAULT '2',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('magenotification_extension_feedbackmessage')} (
  `feedbackmessage_id` int(11) unsigned NOT NULL auto_increment,
  `feedback_id` int(11) unsigned NOT NULL,
  `feedback_code` varchar(255) NOT NULL default '',
  `user` varchar(255) NOT NULL default '',
  `is_customer` tinyint(1) default '2',
  `message` text NOT NULL default '',
  `file` text NOT NULL default '',
  `posted_time` datetime NULL,
  `is_sent` tinyint(1) default '2',
  PRIMARY KEY (`feedbackmessage_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 