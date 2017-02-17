<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();

	$this->getConnection()->dropColumn($this->getTable('attributesplash_group'), 'display_mode');
	$this->getConnection()->addColumn($this->getTable('attributesplash_group'), 'display_mode', " varchar(40) NOT NULL default 'PRODUCTS' AFTER meta_keywords");

	$this->endSetup();