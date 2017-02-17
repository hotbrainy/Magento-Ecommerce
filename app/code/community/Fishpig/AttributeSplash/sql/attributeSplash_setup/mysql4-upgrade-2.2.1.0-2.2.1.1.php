<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

	$this->startSetup();
	
	try {
		// Create temporary field
		$this->getConnection()->addColumn($this->getTable('attributesplash_page'), 'option_id_temp', " int(11) unsigned NOT NULL default 0 AFTER option_id");
		
		// Copy over data
		$this->run("UPDATE {$this->getTable('attributesplash_page')} SET option_id_temp = option_id");

		// Drop old field
		$this->run("ALTER TABLE {$this->getTable('attributesplash_page')} DROP KEY `FK_OPTION_ID_SPLASH_PAGE`");
		
		// Drop column
		$this->getConnection()->dropColumn($this->getTable('attributesplash_page'), 'option_id');
		
		// Rename temp field
		$this->getConnection()->changeColumn($this->getTable('attributesplash_page'), 'option_id_temp', 'option_id', 'int(11) unsigned NOT NULL default 0 AFTER page_id');
		
		// Add constraint
		$this->getConnection()->addConstraint('FK_OPTION_ID_SPLASH_PAGE', $this->getTable('attributesplash_page'), 'option_id', $this->getTable('eav_attribute_option'), 'option_id');
		
		$this->endSetup();
	}
	catch (Exception $e) {
		Mage::logException($e);
	}
