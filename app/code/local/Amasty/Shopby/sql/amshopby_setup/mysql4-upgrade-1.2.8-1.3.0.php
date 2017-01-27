<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|single_choice:1
 * @Migration field_exist:amshopby/filter|collapsed:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `single_choice` TINYINT(1) NOT NULL;
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `collapsed` TINYINT(1) NOT NULL;
"); 

$this->endSetup();