<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|use_and_logic:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `use_and_logic` TINYINT(1) NOT NULL DEFAULT 0;
");

$this->endSetup();
