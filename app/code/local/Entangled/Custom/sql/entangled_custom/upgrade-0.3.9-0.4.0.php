<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->setConfigData("customer/address/dob_show","opt");

$installer->endSetup();