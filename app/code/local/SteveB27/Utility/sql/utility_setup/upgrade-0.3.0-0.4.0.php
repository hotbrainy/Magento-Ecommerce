<?php

$installer = $this;
$installer->startSetup();

$newAttributes = array(
	array(
		'entity'	=> 'author',
		'code'		=> 'best_seller_international',
		'label'		=> 'International Best Selling Author',
		'type'		=> 'boolean',
		//'option'	=> array('values' => array()),
		),
	array(
		'entity'	=> 'author',
		'code'		=> 'award_winning',
		'label'		=> 'Award-Winning Author',
		'type'		=> 'boolean',
		//'option'	=> array('values' => array()),
		),
	);
$installer->createAttributes($newAttributes);
 
$installer->endSetup();