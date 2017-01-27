<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->setConfigData("sales/reorder/allow",0);

$installer->endSetup();