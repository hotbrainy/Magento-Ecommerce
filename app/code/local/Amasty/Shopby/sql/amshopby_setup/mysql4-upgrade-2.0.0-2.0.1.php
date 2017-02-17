<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration table_exist:amshopby/page:1
 */

$this->run("
CREATE TABLE `{$this->getTable('amshopby/page')}` (
  `page_id`    mediumint(8) unsigned NOT NULL auto_increment,
  `num`        tinyint(4) unsigned NOT NULL,
  `use_cat`    tinyint(1) NOT NULL,
  `url`        varchar(255) NOT NULL,
  `cms_block`  varchar(255) NOT NULL,
  `title`      varchar(255) NOT NULL,
  `meta_title` varchar(255) NOT NULL,
  `meta_descr` text NOT NULL,
  `cond`       text NOT NULL,
  
  PRIMARY KEY  (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

");

$this->endSetup();