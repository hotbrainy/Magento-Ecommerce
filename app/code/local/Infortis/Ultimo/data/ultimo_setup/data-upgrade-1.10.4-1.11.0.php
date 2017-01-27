<?php

$installer = $this;
$installer->startSetup();


//Size of product image in category view. Update the field with new value.
$catViewWidth = Mage::getStoreConfig('ultimo/category/image_width');
if ($catViewWidth)
{
	Mage::getConfig()->saveConfig('catalog/product_image/small_width', $catViewWidth);
}

//Size of product image in product view. Update the field with new value.
$prodViewWidth = Mage::getStoreConfig('cloudzoom/general/big_image_width');
if ($prodViewWidth)
{
	Mage::getConfig()->saveConfig('catalog/product_image/base_width', $prodViewWidth);
}


$installer->endSetup();
