<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$attributeObject = Mage::getSingleton('eav/config')->getAttribute('customer', 'dob');
$attributeObject->setData('is_required', 0);
$attributeObject->setData('is_visible',  1);
$attributeObject->save();

$installer->endSetup();