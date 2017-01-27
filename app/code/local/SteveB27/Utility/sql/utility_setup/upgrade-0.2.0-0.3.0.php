<?php

$installer = $this;
$installer->startSetup();

$newAttributes = array(
	array(
		'entity'	=> 'catalog_product',
		'code'		=> 'book_series_length',
		'label'		=> 'Series Length',
		'type'		=> 'select',
		'option'	=> array('values' => array()),
		),
	);
$installer->createAttributes($newAttributes);
 
$installer->endSetup();