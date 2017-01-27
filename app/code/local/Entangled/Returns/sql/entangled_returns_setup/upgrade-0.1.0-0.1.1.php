<?php
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('entangled_returns/request'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'User Id')
    ->addColumn('product_sku', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Product Sku')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
    ), 'Order Id')
    ->addColumn('reason', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'Reason')
    ->addColumn('date', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
    ), 'Date')
    ->addColumn('approved', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
    ), 'Approved')
    ->addColumn('approval_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
    ), 'Approval Date');

$installer->getConnection()->createTable($table);

$installer->endSetup();