<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();

	$this->getConnection()->addColumn($this->getTable('attributesplash_page'), 'image_url', " varchar(255) NOT NULL default '' AFTER image");

	$this->endSetup();
