<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();

	/**
	 * Create new splash page table
	 *
	 */
	$this->run("

		CREATE TABLE IF NOT EXISTS {$this->getTable('attributesplash_group_index')} (
			`group_id` int(11) unsigned NOT NULL,
			`store_id` smallint(5) unsigned NOT NULL,
			PRIMARY KEY (`group_id`, `store_id`),
			KEY `FK_GROUP_ID_SPLASH_GROUP_INDEX` (`group_id`),
			CONSTRAINT `FK_GROUP_ID_SPLASH_GROUP_INDEX` FOREIGN KEY (`group_id`) REFERENCES `{$this->getTable('attributesplash_group')}` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			KEY `FK_STORE_ID_SPLASH_GROUP_INDEX` (`store_id`),
			CONSTRAINT `FK_STORE_ID_SPLASH_GROUP_INDEX` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='AttributeSplash: Group Index';
		
		CREATE TABLE IF NOT EXISTS {$this->getTable('attributesplash_page_index')} (
			`page_id` int(11) unsigned NOT NULL,
			`store_id` smallint(5) unsigned NOT NULL,
			PRIMARY KEY (`page_id`, `store_id`),
			KEY `FK_PAGE_ID_SPLASH_PAGE_INDEX` (`page_id`),
			CONSTRAINT `FK_PAGE_ID_SPLASH_PAGE_INDEX` FOREIGN KEY (`page_id`) REFERENCES `{$this->getTable('attributesplash_page')}` (`page_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			KEY `FK_STORE_ID_SPLASH_PAGE_INDEX` (`store_id`),
			CONSTRAINT `FK_STORE_ID_SPLASH_PAGE_INDEX` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='AttributeSplash: Page Index';

	");

	$this->endSetup();
