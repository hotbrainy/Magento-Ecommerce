<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

/**
 * @Migration field_exist:amshopby/page|cms_block_id:1
 */
$this->run("
ALTER TABLE `{$this->getTable('amshopby/page')}`
ADD `cms_block_id` int(11) DEFAULT NULL");

$this->run("
UPDATE `{$this->getTable('amshopby/page')}` v,`{$this->getTable('cms/block')}` b
SET v.`cms_block_id` = b.`block_id`
WHERE b.`identifier` = v.`cms_block`
");

/**
 * @Migration field_exist:amshopby/page|cms_block:0
 */
$this->run("
ALTER TABLE `{$this->getTable('amshopby/page')}`
DROP `cms_block`
");


$this->endSetup();
