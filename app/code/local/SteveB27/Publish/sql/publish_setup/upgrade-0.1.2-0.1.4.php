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
    ->newTable($installer->getTable('publish/author_file'))
    ->addColumn(
		'value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Attribute Type Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Attribute Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Store Ud')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(), 'Entity Id')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(), 'Attribute Value')
    ->setComment('Eav Entity Value Table');
$installer->getConnection()->createTable($table);