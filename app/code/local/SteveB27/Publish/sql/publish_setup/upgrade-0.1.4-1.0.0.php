<?php 
/**
 * Custom Publisher Models
 * 
 * Add custom model types, such as author, which can be used as a product
 * attribute while proviting additional details.
 * 
 * @license 	http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 * @copyright	Steven Brown March 12, 2016
 * @author		Steven Brown <steveb.27@outlook.com>
 */
 
$installer = Mage::getResourceModel('catalog/setup', 'catalog_setup');
$installer->startSetup();

$installer->addAttribute('author', 'real_name', array(
	'type' 				=> 'varchar',
	'backend' 			=> '',
	'frontend' 			=> '',
	'label' 			=> 'Real Name',
	'input' 			=> 'text',
	'source' 			=> '',
	'global' 			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'required' 			=> '0',
	'user_defined' 		=> '1',
	'default' 			=> '',
	'unique' 			=> '0',
	'position'       	=> '12',
	'note'           	=> '',
	'is_visible'        	=> '1',
	'is_wysiwyg_enabled'	=> '0',
	));
$installer->addAttribute('author', 'contract_id', array(
	'type' 				=> 'varchar',
	'backend' 			=> '',
	'frontend' 			=> '',
	'label' 			=> 'Contract ID',
	'input' 			=> 'text',
	'source' 			=> '',
	'global' 			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'required' 			=> '0',
	'user_defined' 		=> '1',
	'default' 			=> '',
	'unique' 			=> '0',
	'position'       	=> '30',
	'note'           	=> '',
	'is_visible'        	=> '1',
	'is_wysiwyg_enabled'	=> '0',
	));
$installer->addAttribute('author', 'agented_by', array(
	'type' 				=> 'varchar',
	'backend' 			=> '',
	'frontend' 			=> '',
	'label' 			=> 'Agented By',
	'input' 			=> 'text',
	'source' 			=> '',
	'global' 			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'required' 			=> '0',
	'user_defined' 		=> '1',
	'default' 			=> '',
	'unique' 			=> '0',
	'position'       	=> '90',
	'note'           	=> '',
	'is_visible'        	=> '1',
	'is_wysiwyg_enabled'	=> '0',
	));

$installer->endSetup();