<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->setConfigData("onestepcheckout/exclude_fields/enable_giftcard",0);

$installer->endSetup();