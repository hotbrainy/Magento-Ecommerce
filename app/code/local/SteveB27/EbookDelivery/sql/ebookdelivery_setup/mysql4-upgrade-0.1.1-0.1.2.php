<?php

$installer = $this;
 
$installer->startSetup();

try
{
	$installer->addAttribute(Mage_Sales_Model_Order::ENTITY, 'delivery_devices', array(
		'group'         => 'General',
		'type'          => 'varchar',
		'default'       => '0',
		'input'         => 'text',
		'label'         => 'Delivery Devices',
		'source'        => '',
		'visible'       => true,
		'required'      => false,
		'visible_on_front' => false,
		'user_defined'  =>  false
	));
}
catch (Exception $ex) { }

$installer->endSetup();