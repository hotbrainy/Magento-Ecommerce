<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();

	$this->run("
	
		CREATE TABLE IF NOT EXISTS {$this->getTable('attributesplash_page_store')} (
			`page_id` int(11) unsigned NOT NULL auto_increment,
			`store_id` smallint(5) unsigned NOT NULL default 0,
			PRIMARY KEY (`page_id`, `store_id`),
			KEY `FK_PAGE_ID_SPLASH_PAGE_STORE` (`page_id`),
			CONSTRAINT `FK_PAGE_ID_SPLASH_PAGE_STORE` FOREIGN KEY (`page_id`) REFERENCES `{$this->getTable('attributesplash_page')}` (`page_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			KEY `FK_STORE_ID_SPLASH_PAGE_STORE` (`store_id`),
			CONSTRAINT `FK_STORE_ID_SPLASH_PAGE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='AttributeSplash: Page / Store';
		
		ALTER TABLE {$this->getTable('attributesplash_page_store')} ADD UNIQUE (`page_id`,`store_id`);

		CREATE TABLE IF NOT EXISTS {$this->getTable('attributesplash_group_store')} (
			`group_id` int(11) unsigned NOT NULL auto_increment,
			`store_id` smallint(5) unsigned NOT NULL default 0,
			PRIMARY KEY (`group_id`, `store_id`),
			KEY `FK_PAGE_ID_SPLASH_GROUP_STORE` (`group_id`),
			CONSTRAINT `FK_PAGE_ID_SPLASH_GROUP_STORE` FOREIGN KEY (`group_id`) REFERENCES `{$this->getTable('attributesplash_group')}` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
			KEY `FK_STORE_ID_SPLASH_GROUP_STORE` (`store_id`),
			CONSTRAINT `FK_STORE_ID_SPLASH_GROUP_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
		) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='AttributeSplash: Group / Store';
		
		ALTER TABLE {$this->getTable('attributesplash_group_store')} ADD UNIQUE (`group_id`,`store_id`);
		
	");

	foreach(array('page', 'group') as $type) {
		try {
			$select = $this->getConnection()
				->select()
				->from($this->getTable('attributesplash_' . $type), array($type . '_id', 'store_id'));
				
			if ($results = $this->getConnection()->fetchAll($select)) {
				foreach($results as $result) {
					$this->getConnection()->insert($this->getTable('attributesplash_' . $type . '_store'), $result);
				}
			}
		
			$this->getConnection()->dropColumn($this->getTable('attributesplash_' . $type), 'store_id');
		}
		catch (Exception $e) {
			Mage::logException($e);
		}
	}

	$this->endSetup();
