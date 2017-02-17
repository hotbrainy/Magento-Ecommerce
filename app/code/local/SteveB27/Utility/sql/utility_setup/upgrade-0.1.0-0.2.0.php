<?php

$installer = $this;
$installer->startSetup();

$newAttributes = array(
	array(
		'entity'	=> 'author',
		'code'		=> 'best_seller_usa_today',
		'label'		=> 'USA Today Best Selling Author',
		'type'		=> 'boolean',
		//'option'	=> array('values' => array()),
		),
	array(
		'entity'	=> 'author',
		'code'		=> 'best_seller_ny_times',
		'label'		=> 'NY Times Best Selling Author',
		'type'		=> 'boolean',
		//'option'	=> array('values' => array()),
		),
	array(
		'entity'	=> 'catalog_product',
		'code'		=> 'asin',
		'label'		=> 'Amazon Standard Identification Number',
		'type'		=> 'text',
		//'option'	=> array('values' => array()),
		),
	);
$installer->createAttributes($newAttributes);
 
$installer->endSetup();