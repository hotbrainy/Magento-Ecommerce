<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->setConfigData("payment/customercredit/title","Rewards Points");

$installer->endSetup();