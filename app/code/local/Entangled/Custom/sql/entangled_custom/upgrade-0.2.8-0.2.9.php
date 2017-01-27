<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->setConfigData("mageworx_customercredit/email_config/send_email_templates",0);

$installer->endSetup();