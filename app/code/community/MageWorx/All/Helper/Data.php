<?php
/**
 * MageWorx
 * All Extension
 *
 * @category   MageWorx
 * @package    MageWorx_All
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_All_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getDateForFilename()
    {
        return Mage::getSingleton('core/date')->date('Y-m-d_H-i-s');
    }
}