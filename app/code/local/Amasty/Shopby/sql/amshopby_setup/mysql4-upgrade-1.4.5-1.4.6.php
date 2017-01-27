<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|seo_noindex:1
 * @Migration field_exist:amshopby/filter|seo_nofollow:1
 * @Migration field_exist:amshopby/filter|seo_rel:1
 */
$this->run("
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `seo_noindex`  TINYINT(1) NOT NULL;
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `seo_nofollow` TINYINT(1) NOT NULL;
    ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `seo_rel`      TINYINT(1) NOT NULL;
"); 

$this->endSetup();