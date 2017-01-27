<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();

	$this->getConnection()->addColumn($this->getTable('attributesplash_page'), 'created_at', " timestamp");
	$this->getConnection()->addColumn($this->getTable('attributesplash_page'), 'updated_at', " timestamp");

	$this->getConnection()->update($this->getTable('attributesplash_page'), array('updated_at' => now(), 'created_at' => now()), '');
	
	$this->getConnection()->addColumn($this->getTable('attributesplash_group'), 'created_at', " timestamp");
	$this->getConnection()->addColumn($this->getTable('attributesplash_group'), 'updated_at', " timestamp");

	$this->getConnection()->update($this->getTable('attributesplash_group'), array('updated_at' => now(), 'created_at' => now()), '');

	$this->endSetup();
