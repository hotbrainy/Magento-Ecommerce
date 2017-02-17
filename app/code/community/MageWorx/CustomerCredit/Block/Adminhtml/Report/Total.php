<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Report_Total extends Mage_Adminhtml_Block_Abstract
{
    protected function _toHtml() {
        $html = parent::_toHtml();
        $html .=  RAND(1,11111);
        return $html;
    }
}