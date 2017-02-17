<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
	
	$this->startSetup();

	try {
		Mage::getSingleton('attributeSplash/indexer')->reindexAll();
	}
	catch (Exception $e) {
		Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		Mage::getSingleton('adminhtml/session')->addError('Please reindex the Attribute Splash Pages');
		Mage::logException($e);
	}

	$this->endSetup();
