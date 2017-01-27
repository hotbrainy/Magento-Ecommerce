<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/page|cats:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/page')}` ADD COLUMN `cats` TEXT NOT NULL;
"); 

$this->endSetup();