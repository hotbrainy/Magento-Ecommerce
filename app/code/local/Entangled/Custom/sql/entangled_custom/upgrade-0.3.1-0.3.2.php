<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$attribute  = array(
    'type' => 'text',
    'input' => 'text',
    'label' => 'Newsletter authors',
    'global' => 1,
    'visible' => 0,
    'default' => '0',
    'required' => 0,
    'user_defined' => 0,
    'used_in_forms' => array(
        'adminhtml_customer',
    ),
);

$installer->addAttribute('customer', 'author_ids', $attribute);
$installer->endSetup();