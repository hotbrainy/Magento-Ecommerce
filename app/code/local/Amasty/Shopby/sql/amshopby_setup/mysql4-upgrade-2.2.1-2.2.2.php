<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/value|img_small_hover:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/value')}` 
    ADD COLUMN `img_small_hover` VARCHAR(255) NOT NULL
"); 

$this->endSetup();