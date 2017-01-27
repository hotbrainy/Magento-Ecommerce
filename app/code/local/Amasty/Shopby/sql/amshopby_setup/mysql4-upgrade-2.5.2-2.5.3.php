<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|include_in:1
 */
$this->run("
ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `include_in` VARCHAR(256) NOT NULL;
");

$this->endSetup();