<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */

/** @var Mage_Sales_Model_Mysql4_Setup $installer */
$installer = $this;
$installer->startSetup();

// 2.8.0
if ($installer->tableExists($this->getTable('customercredit_credit')) && !$installer->tableExists($this->getTable('mageworx_customercredit/credit'))) {
    $installer->getConnection()->renameTable($this->getTable('customercredit_credit'), $this->getTable('mageworx_customercredit/credit'));
}

if ($installer->tableExists($this->getTable('customercredit_credit_log')) && !$installer->tableExists($this->getTable('mageworx_customercredit/credit_log'))) {
    $installer->getConnection()->renameTable($this->getTable('customercredit_credit_log'), $this->getTable('mageworx_customercredit/credit_log'));
}

if ($installer->tableExists($this->getTable('customercredit_code')) && !$installer->tableExists($this->getTable('mageworx_customercredit/code'))) {
    $installer->getConnection()->renameTable($this->getTable('customercredit_code'), $this->getTable('mageworx_customercredit/code'));
}

if ($installer->tableExists($this->getTable('customercredit_code_log')) && !$installer->tableExists($this->getTable('mageworx_customercredit/code_log'))) {
    $installer->getConnection()->renameTable($this->getTable('customercredit_code_log'), $this->getTable('mageworx_customercredit/code_log'));
}

if ($installer->tableExists($this->getTable('customercredit_rules')) && !$installer->tableExists($this->getTable('mageworx_customercredit/rules'))) {
    $installer->getConnection()->renameTable($this->getTable('customercredit_rules'), $this->getTable('mageworx_customercredit/rules'));
}

if ($installer->tableExists($this->getTable('customercredit_rules_customer')) && !$installer->tableExists($this->getTable('mageworx_customercredit/rules_customer'))) {
    $installer->getConnection()->renameTable($this->getTable('customercredit_rules_customer'), $this->getTable('mageworx_customercredit/rules_customer'));
}

if ($installer->tableExists($this->getTable('customercredit_rules_customer_action')) && !$installer->tableExists($this->getTable('mageworx_customercredit/rules_customer_action'))) {
    $installer->getConnection()->renameTable($this->getTable('customercredit_rules_customer_action'), $this->getTable('mageworx_customercredit/rules_customer_action'));
}

if ($installer->tableExists($this->getTable('customercredit_rules_customer_log')) && !$installer->tableExists($this->getTable('mageworx_customercredit/rules_customer_log'))) {
    $installer->getConnection()->renameTable($this->getTable('customercredit_rules_customer_log'), $this->getTable('mageworx_customercredit/rules_customer_log'));
}


/**
 * Create table mageworx_customercredit/credit
 */
if (!$installer->tableExists($this->getTable('mageworx_customercredit/credit'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_customercredit/credit'))
        ->addColumn('credit_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Credit ID')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Customer ID')
        ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Website ID')
        ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable'  => false,
        ), 'Value')
        ->addColumn('expiration_time', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
            'nullable'  => false,
        ), 'Expiration Time')
        ->setComment('Customer Credits');
    $installer->getConnection()->createTable($table);
}
//2.2.4 -> 2.3.0
if (!$installer->getConnection()->tableColumnExists($installer->getTable('mageworx_customercredit/credit'), 'expiration_time')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/credit'),
        'expiration_time',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
            'nullable'  => false,
            'comment'   => 'Expiration Time',
        )
    );
}

//reset indexes
$indexList = $installer->getConnection()->getIndexList($this->getTable('mageworx_customercredit/credit'));
foreach ($indexList as $indexProp) {
    if ($indexProp['KEY_NAME'] == 'PRIMARY') {
        continue;
    }
    $installer->getConnection()->dropIndex($this->getTable('mageworx_customercredit/credit'), $indexProp['KEY_NAME']);
}
$installer->getConnection()->addIndex(
    $installer->getTable('mageworx_customercredit/credit'),
    $installer->getIdxName('mageworx_customercredit/credit', array('customer_id')),
    array('customer_id')
);


/**
 * Create table mageworx_customercredit/credit_log
 */
if (!$installer->tableExists($this->getTable('mageworx_customercredit/credit_log'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_customercredit/credit_log'))
        ->addColumn('log_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Credit Log ID')
        ->addColumn('credit_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Credit ID')
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
        ), 'Order ID')
        ->addColumn('rules_customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
        ), 'Rules Customer ID')
        ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Rule ID')
        ->addColumn('action_type', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Action Type')
        ->addColumn('action_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        ), 'Action Date')
        ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable'  => false,
        ), 'Value')
        ->addColumn('value_change', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable'  => false,
        ), 'Value Change')
        ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable'  => false,
        ), 'Comment')
        ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Website ID')
        ->addColumn('staff_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
        ), 'Staff Name')
        ->setComment('Customer Credit Log');
    $installer->getConnection()->createTable($table);
}
// 1.1.4 -> 1.2.0
if (!$installer->getConnection()->tableColumnExists($installer->getTable('mageworx_customercredit/credit_log'), 'order_id')) {

    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/credit_log'),
        'order_id',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned'  => true,
            'after'     => 'credit_id',
            'comment'   => 'Order ID',
        )
    );

    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/credit_log'),
        'rules_customer_id',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned'  => true,
            'after'     => 'order_id',
            'comment'   => 'Rules Customer ID',
        )
    );
}
//1.9.0 -> 2.0.0
if (!$installer->getConnection()->tableColumnExists($this->getTable('mageworx_customercredit/credit_log'), 'rule_id')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/credit_log'),
        'rule_id',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable'  => false,
            'unsigned'  => true,
            'after'     => 'rules_customer_id',
            'comment'   => 'Rule ID',
        )
    );
}
//2.2.4 -> 2.3.0
if (!$installer->getConnection()->tableColumnExists($installer->getTable('mageworx_customercredit/credit_log'), 'website_id')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/credit_log'),
        'website_id',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'unsigned'  => true,
            'nullable'  => false,
            'comment'   => 'Website ID',
        )
    );
}
if (!$installer->getConnection()->tableColumnExists($installer->getTable('mageworx_customercredit/credit_log'), 'staff_name')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/credit_log'),
        'staff_name',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_VARCHAR,
            'length'    => 255,
            'nullable'  => false,
            'comment'   => 'Staff Name',
        )
    );
}

//reset indexes
$indexList = $installer->getConnection()->getIndexList($this->getTable('mageworx_customercredit/credit_log'));
foreach ($indexList as $indexProp) {
    if ($indexProp['KEY_NAME'] == 'PRIMARY') {
        continue;
    }
    $installer->getConnection()->dropIndex($this->getTable('mageworx_customercredit/credit_log'), $indexProp['KEY_NAME']);
}
$installer->getConnection()->addIndex(
    $installer->getTable('mageworx_customercredit/credit_log'),
    $installer->getIdxName('mageworx_customercredit/credit_log', array('rule_id')),
    array('rule_id')
);
$installer->getConnection()->addIndex(
    $installer->getTable('mageworx_customercredit/credit_log'),
    $installer->getIdxName('mageworx_customercredit/credit_log', array('order_id')),
    array('order_id')
);


/**
 * Create table mageworx_customercredit/code
 */
if (!$installer->tableExists($this->getTable('mageworx_customercredit/code'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_customercredit/code'))
        ->addColumn('code_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Credit Code ID')
        ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Website ID')
        ->addColumn('code', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
        ), 'Credit Code')
        ->addColumn('credit', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable'  => false,
        ), 'Credit value')
        ->addColumn('created_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        ), 'Create Date')
        ->addColumn('updated_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        ), 'Update Date')
        ->addColumn('used_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'Date of last Use')
        ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'Date since the code is valid')
        ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'Date the code is valid to')
        ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Is Active')
        ->addColumn('is_onetime', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'nullable'  => false,
            'default'   => 1,
        ), 'Is Onetime')
        ->addColumn('owner_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Owner ID')
        ->setComment('Customer Credit Code');
    $installer->getConnection()->createTable($table);
}

//1.9.0 -> 2.0.0
if (!$installer->getConnection()->tableColumnExists($this->getTable('mageworx_customercredit/code'), 'owner_id')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/code'),
        'owner_id',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'comment'   => 'Owner ID',
        )
    );
}
//2.2.4 -> 2.3.0
if (!$installer->getConnection()->tableColumnExists($installer->getTable('mageworx_customercredit/code'), 'is_onetime')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/code'),
        'is_onetime',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TINYINT,
            'nullable'  => false,
            'default'   => 1,
            'after'     => 'is_active',
            'comment'   => 'Is Onetime',
        )
    );
}


/**
 * Create table mageworx_customercredit/code_log
 */
if (!$installer->tableExists($this->getTable('mageworx_customercredit/code_log'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_customercredit/code_log'))
        ->addColumn('log_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Credit Code Log ID')
        ->addColumn('code_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Credit Code ID')
        ->addColumn('action_type', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Action Type')
        ->addColumn('action_date', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        ), 'Action Date')
        ->addColumn('credit', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'nullable'  => false,
        ), 'Credit Value')
        ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable'  => false,
        ), 'Comment')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Customer ID')
        ->setComment('Customer Credit Code Log');
    $installer->getConnection()->createTable($table);
}
//2.2.4 -> 2.3.0
if (!$installer->getConnection()->tableColumnExists($installer->getTable('mageworx_customercredit/code_log'), 'customer_id')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/code_log'),
        'customer_id',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable'  => false,
            'comment'   => 'Customer ID',
        )
    );
}

//reset indexes
$indexList = $installer->getConnection()->getIndexList($this->getTable('mageworx_customercredit/code_log'));
foreach ($indexList as $indexProp) {
    if ($indexProp['KEY_NAME'] == 'PRIMARY') {
        continue;
    }
    $installer->getConnection()->dropIndex($this->getTable('mageworx_customercredit/code_log'), $indexProp['KEY_NAME']);
}
$installer->getConnection()->addIndex(
    $installer->getTable('mageworx_customercredit/code_log'),
    $installer->getIdxName('mageworx_customercredit/code_log', array('customer_id')),
    array('customer_id')
);
$installer->getConnection()->addIndex(
    $installer->getTable('mageworx_customercredit/code_log'),
    $installer->getIdxName('mageworx_customercredit/code_log', array('code_id')),
    array('code_id')
);
$installer->getConnection()->addIndex(
    $installer->getTable('mageworx_customercredit/code_log'),
    $installer->getIdxName('mageworx_customercredit/code_log', array('action_type')),
    array('action_type')
);


/**
 * Create table mageworx_customercredit/rules
 */
if (!$installer->tableExists($this->getTable('mageworx_customercredit/rules'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_customercredit/rules'))
        ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'Rule ID')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
        ), 'Rule Name')
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
        ), 'Rule Description')
        ->addColumn('is_onetime', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'nullable'  => false,
            'default'   => 1,
        ), 'Is Onetime')
        ->addColumn('qty_dependent', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'nullable'  => false,
        ), 'Is Qty dependent')
        ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'nullable'  => false,
        ), 'Is Active')
        ->addColumn('website_ids', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
        ), 'Website IDs')
        ->addColumn('credit', Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, array(
            'nullable'  => false,
        ), 'Credit Value')
        ->addColumn('customer_group_ids', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
        ), 'Customer Group IDs')
        ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Conditions Serialized')
        ->addColumn('actions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Actions Serialized')
        ->addColumn('rule_type', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
            'nullable'  => false,
        ), 'Rule Type')
        ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'Create Date')
        ->setComment('Customer Credit Rules');
    $installer->getConnection()->createTable($table);
}

// reset column charset which was erroneously set to cp1251 in upgrade file 1.0.0 -> 1.1.0
$resetCharsetColumns = array(
    'name' => array(
        'length'      => 255,
        'comment'   => 'Rule Name',
    ),
    'description' => array(
        'length'      => 255,
        'comment'   => 'Rule Description',
    ),
    'website_ids' => array(
        'length'      => 255,
        'comment'   => 'Website IDs',
    ),
    'customer_group_ids' => array(
        'length'      => 255,
        'comment'   => 'Customer Group IDs',
    ),
    'conditions_serialized' => array(
        'length'      => '2M',
        'comment'   => 'Conditions Serialized',
    ),
    'actions_serialized' => array(
        'length'      => '2M',
        'comment'   => 'Actions Serialized',
    ),
);
foreach ($resetCharsetColumns as $columnName => $columnDefinition) {
    $columnDefinition['type'] = Varien_Db_Ddl_Table::TYPE_TEXT;
    if ($columnDefinition['length'] == 255) {
        $columnDefinition = array_merge($columnDefinition, array(
            'nullable'  => false,
        ));
    }

    $installer->getConnection()->modifyColumn(
        $installer->getTable('mageworx_customercredit/rules'),
        $columnName,
        $columnDefinition
    );

}

// 1.3.0 -> 1.4.0
if (!$installer->getConnection()->tableColumnExists($installer->getTable('mageworx_customercredit/rules'), 'is_onetime')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/rules'),
        'is_onetime',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TINYINT,
            'nullable'  => false,
            'default'   => 1,
            'after'     => 'description',
            'comment'   => 'Is Onetime',
        )
    );
}
//1.9.0 -> 2.0.0
if (!$installer->getConnection()->tableColumnExists($this->getTable('mageworx_customercredit/rules'), 'rule_type')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/rules'),
        'rule_type',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TINYINT,
            'nullable'  => false,
            'comment'   => 'Rule Type',
        )
    );
}
//2.1.1 -> 2.2.0
if (!$installer->getConnection()->tableColumnExists($installer->getTable('mageworx_customercredit/rules'), 'qty_dependent')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/rules'),
        'qty_dependent',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_TINYINT,
            'nullable'  => false,
            'after'     => 'is_onetime',
            'comment'   => 'Is Qty dependent',
        )
    );
}
//2.2.4 -> 2.3.0
$installer->getConnection()->modifyColumn(
    $installer->getTable('mageworx_customercredit/rules'),
    'credit',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length'    => 10,
        'nullable'  => false,
        'comment'   => 'Credit Value',
    )
);
//2.4.2 -> 2.4.3
if (!$installer->getConnection()->tableColumnExists($installer->getTable('mageworx_customercredit/rules'), 'created_at')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/rules'),
        'created_at',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
            'nullable'  => false,
            'comment'   => 'Create Date',
        )
    );
}

//reset indexes
$indexList = $installer->getConnection()->getIndexList($this->getTable('mageworx_customercredit/rules'));
foreach ($indexList as $indexProp) {
    if ($indexProp['KEY_NAME'] == 'PRIMARY') {
        continue;
    }
    $installer->getConnection()->dropIndex($this->getTable('mageworx_customercredit/rules'), $indexProp['KEY_NAME']);
}
$installer->getConnection()->addIndex(
    $installer->getTable('mageworx_customercredit/rules'),
    $installer->getIdxName('mageworx_customercredit/rules', array('rule_type')),
    array('rule_type')
);


/**
 * Create table mageworx_customercredit/rules_customer
 */
if (!$installer->tableExists($this->getTable('mageworx_customercredit/rules_customer'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_customercredit/rules_customer'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'ID')
        ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  => true,
            'nullable'  => false,
        ), 'Rule ID')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
        ), 'Customer ID')
        ->setComment('Customer Credit Rules and Customer relation');
    $installer->getConnection()->createTable($table);
}
// add 'unsigned' attribute for old structure
$installer->getConnection()->modifyColumn(
    $installer->getTable('mageworx_customercredit/rules_customer'),
    'id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'comment'   => 'ID',
    )
);
// add 'unsigned' attribute for old structure
$installer->getConnection()->modifyColumn(
    $installer->getTable('mageworx_customercredit/rules_customer'),
    'rule_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'unsigned'  => true,
        'nullable'  => false,
        'comment'   => 'Rule ID',
    )
);

//reset indexes
$indexList = $installer->getConnection()->getIndexList($this->getTable('mageworx_customercredit/rules_customer'));
foreach ($indexList as $indexProp) {
    if ($indexProp['KEY_NAME'] == 'PRIMARY') {
        continue;
    }
    $installer->getConnection()->dropIndex($this->getTable('mageworx_customercredit/rules_customer'), $indexProp['KEY_NAME']);
}
$installer->getConnection()->addIndex(
    $installer->getTable('mageworx_customercredit/rules_customer'),
    $installer->getIdxName('mageworx_customercredit/rules_customer', array('rule_id', 'customer_id')),
    array('rule_id', 'customer_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);


/**
 * Create table mageworx_customercredit/rules_customer_action
 */
if (!$installer->tableExists($this->getTable('mageworx_customercredit/rules_customer_action'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_customercredit/rules_customer_action'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'ID')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
        ), 'Customer ID')
        ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
        ), 'Rule ID')
        ->addColumn('action_tag', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
        ), 'Action Tag')
        ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
        ), 'Value')
        ->setComment('Credit Rule Customer Action');
    $installer->getConnection()->createTable($table);
}
// add 'unsigned' attribute for old structure
$installer->getConnection()->modifyColumn(
    $installer->getTable('mageworx_customercredit/rules_customer_action'),
    'id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'comment'   => 'ID',
    )
);

//reset indexes
$indexList = $installer->getConnection()->getIndexList($this->getTable('mageworx_customercredit/rules_customer_action'));
foreach ($indexList as $indexProp) {
    if ($indexProp['KEY_NAME'] == 'PRIMARY') {
        continue;
    }
    $installer->getConnection()->dropIndex($this->getTable('mageworx_customercredit/rules_customer_action'), $indexProp['KEY_NAME']);
}
$installer->getConnection()->addIndex(
    $installer->getTable('mageworx_customercredit/rules_customer_action'),
    $installer->getIdxName('mageworx_customercredit/rules_customer_action', array('customer_id', 'rule_id', 'action_tag')),
    array('customer_id', 'rule_id', 'action_tag')
);


/**
 * Create table mageworx_customercredit/rules_customer_log
 */
if (!$installer->tableExists($this->getTable('mageworx_customercredit/rules_customer_log'))) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('mageworx_customercredit/rules_customer_log'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  => true,
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
        ), 'ID')
        ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
        ), 'Rule ID')
        ->addColumn('action_tag', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
        ), 'Action Tag')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable'  => false,
        ), 'Customer ID')
        ->addColumn('value', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable'  => false,
        ), 'Value')
        ->setComment('Credit Rule Customer Log');
    $installer->getConnection()->createTable($table);
}
// add 'unsigned' attribute for old structure
$installer->getConnection()->modifyColumn(
    $installer->getTable('mageworx_customercredit/rules_customer_log'),
    'id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'comment'   => 'ID',
    )
);

//reset indexes
$indexList = $installer->getConnection()->getIndexList($this->getTable('mageworx_customercredit/rules_customer_log'));
foreach ($indexList as $indexProp) {
    if ($indexProp['KEY_NAME'] == 'PRIMARY') {
        continue;
    }
    $installer->getConnection()->dropIndex($this->getTable('mageworx_customercredit/rules_customer_log'), $indexProp['KEY_NAME']);
}
$installer->getConnection()->addIndex(
    $installer->getTable('mageworx_customercredit/rules_customer_log'),
    $installer->getIdxName('mageworx_customercredit/rules_customer_log', array('customer_id', 'rule_id', 'action_tag')),
    array('customer_id', 'rule_id', 'action_tag')
);


// 1.0.0
$installer->addAttribute('quote', 'customer_credit_total', array('type'=>'decimal'));
$installer->addAttribute('quote', 'base_customer_credit_total', array('type'=>'decimal'));

$installer->addAttribute('quote_address', 'customer_credit_amount', array('type'=>'decimal'));
$installer->addAttribute('quote_address', 'base_customer_credit_amount', array('type'=>'decimal'));

$installer->addAttribute('order', 'customer_credit_amount', array('type'=>'decimal'));
$installer->addAttribute('order', 'base_customer_credit_amount', array('type'=>'decimal'));

$installer->addAttribute('order', 'customer_credit_invoiced', array('type'=>'decimal'));
$installer->addAttribute('order', 'base_customer_credit_invoiced', array('type'=>'decimal'));

$installer->addAttribute('order', 'customer_credit_refunded', array('type'=>'decimal'));
$installer->addAttribute('order', 'base_customer_credit_refunded', array('type'=>'decimal'));

$installer->addAttribute('invoice', 'customer_credit_amount', array('type'=>'decimal'));
$installer->addAttribute('invoice', 'base_customer_credit_amount', array('type'=>'decimal'));

$installer->addAttribute('creditmemo', 'customer_credit_amount', array('type'=>'decimal'));
$installer->addAttribute('creditmemo', 'base_customer_credit_amount', array('type'=>'decimal'));


// 1.6.0 -> 1.7.0
if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/invoice_grid'), 'base_customer_credit_amount')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('sales/invoice_grid'),
        'base_customer_credit_amount',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length'    => '12,4',
            'comment'   => 'Base Customer Credit Amount',
        )
    );
}

if (!$installer->getConnection()->tableColumnExists($installer->getTable('sales/creditmemo_grid'), 'base_customer_credit_amount')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('sales/creditmemo_grid'),
        'base_customer_credit_amount',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length'    => '12,4',
            'comment'   => 'Base Customer Credit Amount',
        )
    );
}

// fill invoice grid table
$select = $installer->getConnection()->select()
    ->from(
        array('si' => $this->getTable('sales/invoice')),
        array(
            'base_customer_credit_amount' => 'base_customer_credit_amount',
        )
    )
    ->where('sig.entity_id = si.entity_id');

$updateQuery = $installer->getConnection()->updateFromSelect(
    $select,
    array('sig' => $installer->getTable('sales/invoice_grid'))
);

$installer->getConnection()->query($updateQuery);

// fill creditmemo grid table
$select = $installer->getConnection()->select()
    ->from(
        array('scm' => $this->getTable('sales/creditmemo')),
        array(
            'base_customer_credit_amount' => 'base_customer_credit_amount',
        )
    )
    ->where('scmg.entity_id = scm.entity_id');

$updateQuery = $installer->getConnection()->updateFromSelect(
    $select,
    array('scmg' => $installer->getTable('sales/creditmemo_grid'))
);
$installer->getConnection()->query($updateQuery);

//update config paths
$pathLike = 'mageworx_customers/customercredit_credit/%';
$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->getSelect()->where('path like ?', $pathLike);

foreach ($configCollection as $conf) {
    $path = $conf->getPath();
    $path = str_replace('mageworx_customers/', 'mageworx_customercredit/', $path);
    $path = str_replace('customercredit_credit/', 'main/', $path);
    $conf->setPath($path)->save();
}

$pathLike = 'mageworx_customers/customercredit_%';
$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->getSelect()->where('path like ?', $pathLike);

foreach ($configCollection as $conf) {
    $path = $conf->getPath();
    $path = str_replace('mageworx_customers/customercredit_', 'mageworx_customercredit/', $path);
    $conf->setPath($path)->save();
}

$pathLike = 'sales/totals_sort/customercredit';
$configCollection = Mage::getModel('core/config_data')->getCollection();
$configCollection->getSelect()->where('path like ?', $pathLike);

foreach ($configCollection as $conf) {
    $path = $conf->getPath();
    $path = str_replace('sales/totals_sort/customercredit', 'sales/totals_sort/mageworx_customercredit', $path);
    $conf->setPath($path)->save();
}

//update rule conditions
function recursiveArrayReplace($find, $replace, $array) {
    if (!is_array($array)) {
        return preg_replace($find, $replace, $array);
    }
    $newArray = array();
    foreach ($array as $key => $value) {
        $newArray[$key] = recursiveArrayReplace($find, $replace, $value);
    }
    return $newArray;
}

if ($installer->getConnection()->tableColumnExists($installer->getTable('mageworx_customercredit/rules'), 'conditions_serialized')) {
    $query = $installer->getConnection()->select()->from($installer->getTable('mageworx_customercredit/rules'));
    $results = $installer->getConnection()->fetchAll($query);

    foreach ($results as $row) {
        $conditions = unserialize($row['conditions_serialized']);
        if ($conditions) {
            $conditions = recursiveArrayReplace('/^customercredit\/rules_condition_/', 'mageworx_customercredit/rules_condition_', $conditions);
            $installer->getConnection()->update(
                $installer->getTable('mageworx_customercredit/rules'),
                array('conditions_serialized' => serialize($conditions)),
                'rule_id = '.$row['rule_id']
            );
        }
    }

}

//set customer group config
$customerGroupData = Mage::getResourceModel('customer/group_collection')->setRealGroupsFilter()->loadData();
foreach ($customerGroupData as $customerGroup) {
    $customerGroupIds[] = $customerGroup->getId();
}
$customerGroupIds = implode(",", $customerGroupIds);
Mage::getModel('core/config')->saveConfig('mageworx_customercredit/main/customer_group', $customerGroupIds);

$installer->endSetup();