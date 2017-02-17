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

$installer = $this;
$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('publish/publish_eav_attribute'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute ID')
    ->addColumn('is_global', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Attribute scope')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Attribute position')
    ->addColumn('is_wysiwyg_enabled', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Attribute uses WYSIWYG')
    ->addColumn('is_visible', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Attribute is visible')
    ->setComment('Publish attribute table');
$installer->getConnection()->createTable($table);

$installer->createEntityTables('publish_author');
 
/*
 * Add Entity type
 */
$installer->addEntityType(SteveB27_Publish_Model_Author::ENTITY,Array(
    'entity_model'          		=> 'publish/author',
    'attribute_model'       		=> 'publish/resource_eav_attribute',
    'table'                 		=> 'publish/author',
    'additional_attribute_table'    => 'publish/publish_eav_attribute',
    'increment_model'       		=> '',
    'increment_per_store'   		=> '0',
));

$installer->installEntities();
 
$installer->endSetup();