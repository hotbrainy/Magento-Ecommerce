<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();

	$this->getConnection()->addColumn($this->getTable('attributesplash_group'), 'category_id', "int(11) unsigned default NULL AFTER attribute_id");
	$this->getConnection()->addColumn($this->getTable('attributesplash_page'), 'category_id', "int(11) unsigned default NULL AFTER option_id");

	$this->endSetup();
