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

if (!$installer->getConnection()->tableColumnExists($installer->getTable('mageworx_customercredit/credit'), 'enable_expiration')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/credit'),
        'enable_expiration',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'length'    => '1',
            'comment'   => 'Enable Expiration',
            'nullable'  => true,
            'default'   => '2'
        )
    );
}

if (!$installer->getConnection()->tableColumnExists($installer->getTable('mageworx_customercredit/rules'), 'email_template')) {
    $installer->getConnection()->addColumn(
        $installer->getTable('mageworx_customercredit/rules'),
        'email_template',
        array(
            'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
            'length'    => '6',
            'comment'   => 'Email Template',
            'nullable'  => false,
        )
    );
}

$installer->endSetup();