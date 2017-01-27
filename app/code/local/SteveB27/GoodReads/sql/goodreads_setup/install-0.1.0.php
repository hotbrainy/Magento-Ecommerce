<?php
$installer = $this;
$installer->startSetup();

$installer->run("
    CREATE TABLE `{$installer->getTable('goodreads/review')}` (
      `review_id` int(11) NOT NULL auto_increment,
      `product_id` int(11),
      `goodreads_id` text,
      `isbn` text,
      `average_rating` decimal(3,2),
      `ratings_count` int(11),
      `text_reviews_count` int(11),
      `reviews_widget` text,
      `timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
      PRIMARY KEY  (`review_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();