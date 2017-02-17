<?php
/**
 * MageWorx
 * All Extension
 *
 * @category   MageWorx
 * @package    MageWorx_All
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_All_Model_System_Config_Source_Cms_Blocks extends MageWorx_All_Model_System_Config_Source_Cms_AbstractSource
{
    protected function getModel()
    {
        return Mage::getModel('cms/block');
    }
}