<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

function alterPageMultipleStores($setup)
{
    /**
     * @Migration field_exist:amshopby/page|stores:1
     * @Migration field_exist:amshopby/page|store_id:0
     */
    $table = $setup->getTable('amshopby/page');

    $setup->run("ALTER TABLE `{$table}` ADD `stores` TEXT NOT NULL AFTER `page_id`");
    $setup->run("UPDATE `{$table}` SET `stores` = `store_id`");
    $setup->run("ALTER TABLE `{$table}` DROP FOREIGN KEY `FK_AMSHOPBY_PAGE_CORE_STORE`");
    $setup->run("ALTER TABLE {$table} DROP INDEX IDX_AMSHOPBY_PAGE_STORE_VIEW_ID");
    $setup->run("ALTER TABLE `{$table}` DROP `store_id`");
}

function alterSliderStep($setup)
{
    $table = $setup->getTable('amshopby/filter');

    $setup->run("ALTER TABLE `{$table}` CHANGE `slider_decimal` `slider_decimal` DECIMAL(6,2) NOT NULL DEFAULT '1'");
    $setup->run("UPDATE `{$table}` SET slider_decimal = POW(10, -slider_decimal)");
}

function enlargeValueMultistoreFields($setup)
{
    $table = $setup->getTable('amshopby/value');

    $setup->run("ALTER TABLE {$table} CHANGE COLUMN `title` `title` TEXT NOT NULL");
    $setup->run("ALTER TABLE {$table} CHANGE COLUMN `meta_title` `meta_title` TEXT NOT NULL");
    $setup->run("ALTER TABLE {$table} CHANGE COLUMN `meta_descr` `meta_descr` TEXT");
    $setup->run("ALTER TABLE {$table} CHANGE COLUMN `meta_kw` `meta_kw` TEXT NOT NULL");
}

alterPageMultipleStores($this);
alterSliderStep($this);
enlargeValueMultistoreFields($this);

$this->endSetup();
