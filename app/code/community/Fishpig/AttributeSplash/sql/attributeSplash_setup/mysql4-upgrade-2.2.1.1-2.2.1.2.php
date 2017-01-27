<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

	$this->startSetup();

	$this->getConnection()->addColumn($this->getTable('attributesplash_page'), 'page_layout', " varchar(32) default NULL AFTER layout_update_xml");
	$this->getConnection()->addColumn($this->getTable('attributesplash_group'), 'page_layout', " varchar(32) default NULL AFTER layout_update_xml");

	$this->endSetup();
