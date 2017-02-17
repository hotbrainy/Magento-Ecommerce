<?php

$installer = $this;
$installer->startSetup();


//Update the field with new default value
$sidePadding = Mage::getStoreConfig('ultimo_design/page/content_padding_side');
if ($sidePadding)
{
	Mage::getConfig()->saveConfig('ultimo_design/page/content_padding_side', '12');
}


$installer->endSetup();
