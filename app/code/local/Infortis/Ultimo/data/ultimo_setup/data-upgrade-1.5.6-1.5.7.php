<?php

$installer = $this;
$installer->startSetup();


//If border enabled, update the field with new default value (width of the border)
$border = Mage::getStoreConfig('ultimo_design/nav/border');
if ($border > 0)
{
	Mage::getConfig()->saveConfig('ultimo_design/nav/border', '5');
}


$installer->endSetup();
