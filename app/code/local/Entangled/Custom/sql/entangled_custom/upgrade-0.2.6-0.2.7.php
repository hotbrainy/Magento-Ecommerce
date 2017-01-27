<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->setConfigData("mageworx_customercredit/main/exchange_rate",1000);

$installer->endSetup();