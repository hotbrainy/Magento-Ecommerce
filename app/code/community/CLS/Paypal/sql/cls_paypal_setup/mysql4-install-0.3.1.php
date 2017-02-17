<?php
/**
 * Classy Llama
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email to us at
 * support+paypal@classyllama.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module
 * to newer versions in the future. If you require customizations of this
 * module for your needs, please write us at sales@classyllama.com.
 *
 * To report bugs or issues with this module, please email support+paypal@classyllama.com.
 * 
 * @category   CLS
 * @package    Paypal
 * @copyright  Copyright (c) 2014 Classy Llama Studios, LLC (http://www.classyllama.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
 
$installer->run("

CREATE TABLE IF NOT EXISTS `{$this->getTable('cls_paypal/customerstored')}` (
  `stored_card_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Stored_card_id',
  `transaction_id` varchar(255) NOT NULL COMMENT 'Transaction_id',
  `customer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Customer_id',
  `cc_type` varchar(255) NOT NULL COMMENT 'Cc_type',
  `cc_last4` varchar(255) NOT NULL COMMENT 'Cc_last4',
  `cc_exp_month` varchar(255) NOT NULL COMMENT 'Cc_exp_month',
  `cc_exp_year` varchar(255) NOT NULL COMMENT 'Cc_exp_year',
  `date` datetime DEFAULT NULL,
  `payment_method` varchar(255) NOT NULL COMMENT 'Payment_method',
  PRIMARY KEY (`stored_card_id`),
  KEY `FK_CLS_PAYPAL_CSTR_STORED_CSTR_ID_CSTR_ENTT_ENTT_ID` (`customer_id`),
  CONSTRAINT `FK_CLS_PAYPAL_CSTR_STORED_CSTR_ID_CSTR_ENTT_ENTT_ID` FOREIGN KEY (`customer_id`) REFERENCES `{$this->getTable('customer/entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='cls_paypal_customer_stored';

");
  
$installer->endSetup();
