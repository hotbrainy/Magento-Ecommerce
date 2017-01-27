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

$social = array(
	'twitter'		=> 'Twitter',
	'facebook'		=> 'Facebook',
	'googleplus'	=> 'Google+',
	'youtube'		=> 'YouTube',
	'vimeo'			=> 'Vimeo',
	'wordpress'		=> 'WordPress',
	'pinterest'		=> 'Pinterest',
	'linkedin'		=> 'LinkedIn',
	'blogger'		=> 'Blogger',
	'amazon'		=> 'Amazon',
);

$position = 50;

foreach($social as $key => $label) {
	$installer->addAttribute('author', 'social_'.$key, array(
		'type' 				=> 'varchar',
		'backend' 			=> '',
		'frontend' 			=> '',
		'label' 			=> $label . ' Url',
		'input' 			=> 'text',
		'source' 			=> '',
		'global' 			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
		'required' 			=> '0',
		'user_defined' 		=> '1',
		'default' 			=> '',
		'unique' 			=> '0',
		'position'       	=> $position,
		'note'           	=> '',
		'is_visible'        	=> '1',
		'is_wysiwyg_enabled'	=> '0',
	));
	$position++;
}

$installer->endSetup();