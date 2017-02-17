<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/filter|show_search:1
 */
$this->run("

ALTER TABLE `{$this->getTable('amshopby/filter')}` ADD `show_search` TINYINT( 1 ) NOT NULL ,
ADD `slider_decimal` TINYINT( 1 ) NOT NULL ;


");
 
$this->endSetup();