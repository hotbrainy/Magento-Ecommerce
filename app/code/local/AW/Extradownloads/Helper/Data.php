<?php

/**
 * Helper for typically jobs
 */
class AW_Extradownloads_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Compare param $version with magento version
     * @param string $version
     * @return boolean
     */
    public function checkVersion($version)
    {
        return version_compare(Mage::getVersion(), $version, '>=');
    }
}