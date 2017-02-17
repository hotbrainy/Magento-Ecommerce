<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
$this->startSetup();

$table = $this->getTable('core/config_data');

$this->run("
UPDATE `{$table}` n
INNER JOIN `{$table}` o ON o.`path` = 'amshopby/general/title_separator'
SET n.`value` = o.`value`
WHERE n.`path` = 'amshopby/meta/title_separator'
");

$this->run("
UPDATE `{$table}` n
INNER JOIN `{$table}` o ON o.`path` = 'amshopby/general/description_separator'
SET n.`value` = o.`value`
WHERE n.`path` = 'amshopby/meta/description_separator'
");

$this->endSetup();
