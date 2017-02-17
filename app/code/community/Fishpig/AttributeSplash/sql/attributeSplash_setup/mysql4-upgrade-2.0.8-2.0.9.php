<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();

	try {
		$this->getConnection()->addColumn($this->getTable('attributesplash_page'), 'thumbnail', " varchar(255) NOT NULL default '' ");
	}
	catch (Exception $e) {
		// Ignore exception. This is here to fix a silly issue
	}
	
	$this->endSetup();
