<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

Mage::getModel('cms/block')
    ->setData([
        'title'         =>  'Genre Ad Block 1',
        'identifier'    =>  'genre-ad-block-1',
        'content'       =>  '<img src="{{skin url='."'images/AD_267_125.png'".'}}" alt=""/>',
        'stores'         =>  array(Mage::app()->getStore()->getStoreId())
    ])
    ->save();

Mage::getModel('cms/block')
    ->setData([
        'title'         =>  'Genre Ad Block 2',
        'identifier'    =>  'genre-ad-block-2',
        'content'       =>  '<img src="{{skin url='."'images/AD_267_125.png'".'}}" alt=""/>',
        'stores'         =>  array(Mage::app()->getStore()->getStoreId())
    ])
    ->save();

$installer->endSetup();