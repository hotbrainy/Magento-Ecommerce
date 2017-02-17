<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Rules_Grid_Website extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $websiteHash = Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash();
        $data = $row->getData($this->getColumn()->getIndex());
        $rowWebsites = explode(',', $data);
        $html = array();
        foreach ($rowWebsites as $websiteId) {
            if(isset($websiteHash[$websiteId])) {
                $html[] = $websiteHash[$websiteId];
            }
        }
        if(count($html)) {
            return join(", ",$html);
        }
        
        return parent::render($row);
    }
   
}