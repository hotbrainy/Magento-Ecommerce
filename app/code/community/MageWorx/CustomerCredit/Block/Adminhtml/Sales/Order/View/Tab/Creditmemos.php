<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Sales_Order_View_Tab_Creditmemos extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Creditmemos
{
    public function setCollection($collection){
        if (Mage::helper('mageworx_customercredit')->isEnabledCreditColumnsInGridOrderViewTabs()) $collection->addFieldToSelect('base_customer_credit_amount');
        $this->_collection = $collection;
    }

    protected function _prepareColumns() {
        if (Mage::helper('mageworx_customercredit')->isEnabledCreditColumnsInGridOrderViewTabs()) {
            $this->addColumnAfter('credit_amount', array(
                'header'    => Mage::helper('mageworx_customercredit')->__('Credit'),
                'index'     => 'base_customer_credit_amount',
                'type'      => 'currency',
                'currency'  => 'base_currency_code',      
                ), 'state');
        }
        return parent::_prepareColumns();
    }
}
