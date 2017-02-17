<?php

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/page|meta_kw:1
 * @Migration field_exist:amshopby/value|meta_kw:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/page')}` ADD COLUMN `meta_kw` varchar(255) NOT NULL;
    ALTER TABLE `{$this->getTable('amshopby/value')}` ADD COLUMN `meta_kw` varchar(255) NOT NULL;
");

$this->endSetup();