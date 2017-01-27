<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
$magentoVersion = Mage::getVersion();
if (version_compare($magentoVersion, '1.8', '>=')){
    class MageWorx_CustomerCredit_Block_Customer_View_Abstract extends Mage_Catalog_Block_Product_Abstract {}
} else {
    class MageWorx_CustomerCredit_Block_Customer_View_Abstract extends Mage_Core_Block_Template {}
}