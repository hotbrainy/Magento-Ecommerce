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
/* $Id: Data.php 4 2013-11-05 07:31:07Z linhnt $ */

class Ma2_FeaturedProducts_Helper_Data extends Mage_Core_Helper_Abstract {

    const PATH_PAGE_HEADING = 'featuredproducts/standalone/heading';
    const DEFAULT_LABEL = 'Featured Products';

    public function getCmsBlockLabel() {
        $configValue = Mage::getStoreConfig(self::PATH_CMS_HEADING);
        return strlen($configValue) > 0 ? $configValue : self::DEFAULT_LABEL;
    }

    public function getPageLabel() {
        $configValue = Mage::getStoreConfig(self::PATH_PAGE_HEADING);
        return strlen($configValue) > 0 ? $configValue : self::DEFAULT_LABEL;
    }

    public function getIsActive() {
        return (bool) Mage::getStoreConfig('featuredproducts/standalone/active');
    }

}
?>