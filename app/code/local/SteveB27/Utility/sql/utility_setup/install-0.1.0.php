<?php

$installer = $this;
$installer->startSetup();

$newAttributes = array(
	array(
		'entity'	=> 'catalog_product',
		'code'		=> 'book_series',
		'label'		=> 'Series',
		'type'		=> 'text',
		//'option'	=> array('values' => array()),
		),
	array(
		'entity'	=> 'catalog_product',
		'code'		=> 'book_series_2',
		'label'		=> 'Order in Series',
		'type'		=> 'int',
		//'option'	=> array('values' => array()),
		),
	array(
		'entity'	=> 'catalog_product',
		'code'		=> 'category_imprint',
		'label'		=> 'Imprint Category',
		'type'		=> 'multiselect',
		'option'	=> array('values' => array()),
		),
	array(
		'entity'	=> 'catalog_product',
		'code'		=> 'category_genre',
		'label'		=> 'Genre',
		'type'		=> 'multiselect',
		'option'	=> array('values' => array()),
		),
	array(
		'entity'	=> 'catalog_product',
		'code'		=> 'release_date',
		'label'		=> 'Release Date',
		'type'		=> 'datetime',
		//'option'	=> array('values' => array()),
		),
	array(
		'entity'	=> 'catalog_product',
		'code'		=> 'book_page_count',
		'label'		=> 'Page Count',
		'type'		=> 'int',
		//'option'	=> array('values' => array()),
		),
	);
$installer->createAttributes($newAttributes);
 
$installer->endSetup();