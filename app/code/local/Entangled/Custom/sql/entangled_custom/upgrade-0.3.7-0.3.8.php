<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$installer->run("
    UPDATE `mgti_eav_attribute_option_value` as `option`
    SET VALUE='Holiday Romances'
    WHERE `option`.value = 'Holidays';
");


$installer->endSetup();