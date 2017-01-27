<?php
/**
 * MagenMarket.com
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Edit or modify this file with yourown risk.
 *
 * @category    Extensions
 * @package     Ma2_FeaturedProducts
 * @copyright   Copyright (c) 2013 MagenMarket. (http://www.magenmarket.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
**/
/* $Id: mysql4-install-1.0.0.php 4 2013-11-05 07:31:07Z linhnt $ */

$installer = $this;

$installer->addAttribute('catalog_product', 'ma2_featured_product', array(
	'group'             => 'General',
	'type'              => 'int',
	'backend'           => '',
	'frontend'          => '',
	'label'             => 'Featured product',
	'input'             => 'boolean',
	'class'             => '',
	'source'            => '',
	'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'visible'           => true,
	'required'          => false,
	'user_defined'      => false,
	'default'           => '0',
	'searchable'        => false,
	'filterable'        => false,
	'comparable'        => false,
	'visible_on_front'  => false,
	'unique'            => false,
	'apply_to'          => 'simple,configurable,virtual,bundle,downloadable',
	'is_configurable'   => false,
	'used_in_product_listing', '1'
));

$installer->updateAttribute('catalog_product', 'ma2_featured_product', 'used_in_product_listing', '1');
$installer->updateAttribute('catalog_product', 'ma2_featured_product', 'is_global', '0');

$installer->endSetup();

?>