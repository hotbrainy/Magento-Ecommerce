<?php
$installer = $this;

$installer->startSetup();

$installer->setConfigData("mageworx_customercredit/main/credit_totals","subtotal,shipping");
$installer->setConfigData(Mage_Tax_Model_Config::CONFIG_XML_PATH_APPLY_AFTER_DISCOUNT,1);

$installer->endSetup();