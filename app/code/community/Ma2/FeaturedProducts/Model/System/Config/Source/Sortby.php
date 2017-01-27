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
/* $Id: Sortby.php 4 2013-11-05 07:31:07Z linhnt $ */

class Ma2_FeaturedProducts_Model_System_Config_Source_Sortby
{
	/*
	 * Prepare data for System->Configuration dropdown
	 * */
	public function toOptionArray()
	{
		return array(
			'price' => Mage::helper('adminhtml')->__('Price'),
			'name' => Mage::helper('adminhtml')->__('Name'),
			'created_at' => Mage::helper('adminhtml')->__('Created date')
		);
	}
}
?>