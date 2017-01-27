<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Rules_Grid_Date extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Date
{
    public function render(Varien_Object $row)
    {
        $data = $row->getData($this->getColumn()->getIndex()); 
//        var_dump($data); exit;
        if($data=="0000-00-00") {
            return "";
        }
        return parent::render($row);
    }
    
}