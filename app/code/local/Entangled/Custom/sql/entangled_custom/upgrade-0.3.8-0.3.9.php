<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$t = Mage::getStoreConfig("payment");
$installer->setConfigData("payment/paypal_express/solution_type","Sole");

$installer->endSetup();