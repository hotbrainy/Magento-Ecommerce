<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|max_options:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `max_options` SMALLINT NOT NULL AFTER `attribute_id` 
");

$this->endSetup();