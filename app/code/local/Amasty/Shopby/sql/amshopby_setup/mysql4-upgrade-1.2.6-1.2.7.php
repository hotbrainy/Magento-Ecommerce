<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|exclude_from:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `exclude_from` VARCHAR(4096) NOT NULL;
"); 

$this->endSetup();