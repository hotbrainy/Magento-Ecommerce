<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|depend_on:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `depend_on`  VARCHAR(255) NOT NULL;
"); 

$this->endSetup();