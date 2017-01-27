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

$installer->addAttribute('catalog_product', 'publish_author', array(
    'group'                         => 'General',
    'type'                          => 'varchar',
    'input'                         => 'multiselect',
    'label'                         => 'Author',
    'backend'                       => 'eav/entity_attribute_backend_array',
    'visible'                       => 1,
    'required'                      => 0,
    'user_defined'                  => 0,
    'configurable'                  => 0,
    'global'                        => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'source'                        => 'publish/catalog_product_attribute_source_author_type'
));

$installer->endSetup();