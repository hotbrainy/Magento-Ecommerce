<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/value|cms_block_id:1
 * @Migration field_exist:amshopby/value|cms_block_bottom_id:1
 */
$this->run("
ALTER TABLE `{$this->getTable('amshopby/value')}`
ADD `cms_block_id` int(11) DEFAULT NULL,
ADD `cms_block_bottom_id` int(11) DEFAULT NULL");

$this->run("
UPDATE `{$this->getTable('amshopby/value')}` v,`{$this->getTable('cms/block')}` b
SET v.`cms_block_id` = b.`block_id`
WHERE b.`identifier` = v.`cms_block`
");

$this->run("
UPDATE `{$this->getTable('amshopby/value')}` v,`{$this->getTable('cms/block')}` b
SET v.`cms_block_bottom_id` = b.`block_id`
WHERE b.`identifier` = v.`cms_block_bottom`
");

/**
 * @Migration field_exist:amshopby/value|cms_block:0
 * @Migration field_exist:amshopby/value|cms_block_bottom:0
 */
$this->run("
ALTER TABLE `{$this->getTable('amshopby/value')}`
DROP `cms_block`,
DROP `cms_block_bottom`;
");


$this->endSetup();
