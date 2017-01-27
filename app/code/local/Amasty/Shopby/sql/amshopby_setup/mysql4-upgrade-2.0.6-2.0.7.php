<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|range:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD COLUMN `range` int NOT NULL;
"); 

$this->endSetup();