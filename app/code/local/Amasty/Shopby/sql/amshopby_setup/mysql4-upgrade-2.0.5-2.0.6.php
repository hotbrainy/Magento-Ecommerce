<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|backend_type:1
 * @Migration field_exist:amshopby/filter|slider_type:1
 * @Migration field_exist:amshopby/filter|from_to_widget:1
 * @Migration field_exist:amshopby/filter|value_label:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `backend_type` VARCHAR(45) NOT NULL DEFAULT '';
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `slider_type` TINYINT(1) NOT NULL;
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `from_to_widget` TINYINT(1) NOT NULL;
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `value_label` VARCHAR(16) NOT NULL;
"); 

$this->endSetup();