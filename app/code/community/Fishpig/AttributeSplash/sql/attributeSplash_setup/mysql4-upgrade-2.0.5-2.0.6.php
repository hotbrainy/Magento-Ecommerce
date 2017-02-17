<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();
	
	$this->run("ALTER TABLE `{$this->getTable('attributesplash_group')}` CHANGE `short_description` `short_description` TEXT;");
	$this->run("ALTER TABLE `{$this->getTable('attributesplash_page')}` CHANGE `short_description` `short_description` TEXT;");

	$this->endSetup();
