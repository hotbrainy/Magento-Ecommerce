<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */ 
class Amasty_Shopby_Block_Adminhtml_Migrations extends Mage_Adminhtml_Block_Template
{
    protected function getInfo()
    {
        /** @var Amasty_Shopby_Helper_Migration $helper */
        $helper = Mage::helper('amshopby/migration');

        return $helper->getMigrationsInfo();
    }

    protected function getRealStateVersion()
    {
        /** @var Amasty_Shopby_Helper_Migration $helper */
        $helper = Mage::helper('amshopby/migration');

        return $helper->getRealStateVersion();
    }
}
