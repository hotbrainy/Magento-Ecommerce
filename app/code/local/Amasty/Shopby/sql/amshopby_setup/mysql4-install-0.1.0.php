<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
$this->startSetup();

/**
 * @Migration table_exist:amshopby/filter:1
 * @Migration table_exist:amshopby/value:1
 */

$this->run("
CREATE TABLE `{$this->getTable('amshopby/value')}` (
  `value_id` mediumint(8) unsigned NOT NULL auto_increment,
  `option_id` mediumint(8) unsigned NOT NULL,
  `filter_id` mediumint(8) unsigned NOT NULL,
  `is_featured` TINYINT(1) NOT NULL,
  `img_small` varchar(255) NOT NULL,
  `img_medium` varchar(255) NOT NULL,
  `img_big` varchar(255) NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_descr` text,
  `title` varchar(255) NOT NULL,
  `descr` text,
  PRIMARY KEY  (`value_id`),
  KEY `option_id` (`option_id`),
  KEY `filter_id` (`filter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$this->getTable('amshopby/filter')}` (
  `filter_id` mediumint(8) unsigned NOT NULL auto_increment,
  `attribute_id` mediumint(8) unsigned NOT NULL,
  `sort_by`        TINYINT(1) NOT NULL,  
  `display_type`   TINYINT(1) NOT NULL,
  `hide_counts`    TINYINT(1) NOT NULL,
  `is_folded`      TINYINT(1) NOT NULL,
  `show_on_list`   TINYINT(1) NOT NULL,
  `show_on_view`   TINYINT(1) NOT NULL,
  PRIMARY KEY  (`filter_id`),  
  KEY `attribute_id` (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$this->endSetup(); 