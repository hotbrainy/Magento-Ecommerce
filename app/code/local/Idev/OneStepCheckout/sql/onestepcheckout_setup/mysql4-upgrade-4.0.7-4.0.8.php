<?php
$installer = $this;
$installer->startSetup();

// let's solve the magneto bug by saving dhl config value that is not in core_config_data by default
// if dhlint is present
// and config value is missing or not set jet

$configObj = Mage::getConfig();
$isDhlint = (int)is_object($configObj->getNode('default/carriers/dhlint'));
$configParam = $configObj->getNode('default/carriers/dhlint/content_type');

if ($isDhlint && empty($configParam)) {
    $configObj->saveConfig('carriers/dhlint/content_type', "D", 'default', 'D');
    $configObj->cleanCache();
}

$installer->endSetup();
?>
