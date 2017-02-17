<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();
	
	try {
		$this->run("ALTER TABLE {$this->getTable('attributesplash_group')} DROP index attribute_id");
	}
	catch (Exception $e) {
		
	}

	$this->endSetup();
