<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


$this->startSetup();

$table = $this->getTable('amshopby/filter');
$this->run("ALTER TABLE `{$table}` ADD `disable_seo_url` TINYINT(1) NOT NULL DEFAULT '0' AFTER `seo_rel`");
$this->run("ALTER TABLE `{$table}` ADD INDEX(`disable_seo_url`)");
$this->run("ALTER TABLE `{$table}` ADD `sort_featured_first` TINYINT(1) NOT NULL DEFAULT '0' AFTER `sort_by`;");
$this->run("ALTER TABLE `{$table}` ADD `number_options_for_show_search` INT(10) NOT NULL DEFAULT '0' AFTER `show_search`;");
$this->run("ALTER TABLE `{$table}` CHANGE COLUMN `slider_decimal` `slider_decimal` DECIMAL(12,4) NOT NULL DEFAULT '1.00' AFTER `number_options_for_show_search`;");
$table = $this->getTable('amshopby/value');
$this->run("ALTER TABLE `{$table}` ADD INDEX(`is_featured`)");

$table = $this->getTable('amshopby/page');
$this->run("ALTER TABLE `{$table}` ADD `bottom_cms_block_id` INT(11) NOT NULL DEFAULT '0' AFTER `cms_block_id`");
$this->run("ALTER TABLE `{$table}` ADD `description` TEXT NOT NULL AFTER `title`");

$this->endSetup();

