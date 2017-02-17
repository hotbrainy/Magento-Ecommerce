<?php
$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE `{$installer->getTable('goodreads/review')}` ADD `reviews_json` TEXT NOT NULL AFTER `reviews_widget`;
");

$installer->endSetup();