<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();

	$this->getConnection()->addColumn($this->getTable('attributesplash_group'), 'include_in_menu', " int(1) unsigned NOT NULL default 1");
	$this->getConnection()->addColumn($this->getTable('attributesplash_page'), 'include_in_menu', " int(1) unsigned NOT NULL default 1");
	
	$this->endSetup();
