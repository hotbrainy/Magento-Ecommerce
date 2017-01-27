<?php
$installer = $this;
$installer->startSetup();

$attribute_id = $installer->getAttributeId('catalog_product', 'book_series_length');
$bookLength = Mage::getModel('amshopby/filter')->load($attribute_id,'attribute_id');
$bookLength->setSingleChoice(0)->save();

$installer->endSetup();
