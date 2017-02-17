<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Rules_Grid_Count extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $data = $row->getData($this->getColumn()->getIndex());
        $url = Mage::getUrl('*/*/info',array("_secure"=>true,"rule_id"=>$row->getRuleId()));
        if($data) {
        return $html = $data." (<a onClick='openInfoGrid(\"".$url."\",\"".$row->getRuleId()."\"); return false;' href='#'>".$this->__('View')."</a>)";
        }
        return parent::render($row);
    }
   
}