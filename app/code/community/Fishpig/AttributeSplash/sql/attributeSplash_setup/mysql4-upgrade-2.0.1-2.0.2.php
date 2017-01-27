<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();

	$this->run("

		CREATE TABLE IF NOT EXISTS {$this->getTable('attributesplash_group')} (
			`group_id` int(11) unsigned NOT NULL auto_increment,
			`attribute_id` smallint(5) unsigned NOT NULL default 0,
			`store_id` smallint(5) unsigned NOT NULL default 0,
			`display_name` varchar(255) NOT NULL default '',
			`short_description` varchar(255) NOT NULL default '',
			`description` TEXT NOT NULL default '',
			`url_key` varchar(180) NOT NULL default '',
			`page_title` varchar(255) NOT NULL default '',
			`meta_description` varchar(255) NOT NULL default '',
			`meta_keywords` varchar(255) NOT NULL default '',
			`display_mode` tinyint(2) unsigned NOT NULL default 0,
			`cms_block` int(11) unsigned NOT NULL default 0,
			`is_enabled` int(1) unsigned NOT NULL default 1,
			PRIMARY KEY (`group_id`),
			KEY `FK_ATTRIBUTE_ID_SPLASH_GROUP` (`attribute_id`),
			CONSTRAINT `FK_ATTRIBUTE_ID_SPLASH_GROUP` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			KEY `FK_STORE_ID_SPLASH_GROUP` (`store_id`),
			CONSTRAINT `FK_STORE_ID_SPLASH_GROUP` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='AttributeSplash: Group';
		
		ALTER TABLE {$this->getTable('attributesplash_group')} ADD UNIQUE (attribute_id, store_id);

	");
	
	$this->getConnection()->addColumn($this->getTable('attributesplash_group'), 'layout_update_xml', " TEXT NOT NULL default ''");
	$this->getConnection()->addColumn($this->getTable('attributesplash_page'), 'is_featured', " int(1) unsigned NOT NULL default 0");
	
	$this->endSetup();
