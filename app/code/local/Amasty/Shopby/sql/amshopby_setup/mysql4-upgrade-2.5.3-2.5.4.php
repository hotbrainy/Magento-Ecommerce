<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/value|url_alias:1
 */
$this->run("
ALTER TABLE `{$this->getTable('amshopby/value')}` ADD  `url_alias` VARCHAR( 255 ) NULL DEFAULT NULL ,
ADD INDEX (  `url_alias` )
");
 
$this->endSetup();