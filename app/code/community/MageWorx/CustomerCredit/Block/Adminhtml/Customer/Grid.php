<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_Customer_Grid extends Mage_Adminhtml_Block_Customer_Grid
{   
    public function setCollection($collection) {
        
        if (Mage::helper('mageworx_customercredit')->isEnabled() && Mage::helper('mageworx_customercredit')->isEnabledCustomerBalanceGridColumn()) {
            $fields = array();
            foreach (Mage::getConfig()->getFieldset('customer_account') as $code=>$node) {
                if ($node->is('name')) {
                    //$this->addAttributeToSelect($code);
                    $fields[$code] = $code;
                }
            }

            $joinCond = 'credit_tbl.customer_id = e.entity_id';
            if (!Mage::helper('mageworx_customercredit')->isScopePerWebsite()) {
                $joinCond .= ' AND credit_tbl.website_id = 0';
            } else if(!Mage::app()->isSingleStoreMode()){
                $joinCond .= ' AND credit_tbl.website_id = e.website_id';
//                $joinCond .= ' AND credit_tbl.website_id != 0';
            }
            $collection->getSelect()->joinLeft(array('credit_tbl'=>$collection->getTable('mageworx_customercredit/credit')),"(".$joinCond.")",'');
//            if (!Mage::helper('mageworx_customercredit')->isScopePerWebsite()) {
//                $collection->addExpressionAttributeToSelect('credit_value', 'IFNULL(credit_tbl.`value`, 0)', $fields);
//            } else {
                $collection->addExpressionAttributeToSelect('credit_value', 'IFNULL(credit_tbl.`value`, 0)', $fields); 
//            }
            
//echo $collection->getSelect()->__toString();
//$sql = $collection->getSelect()->assemble();
            //$collection->getSelect()->reset()->from(array('e' => new Zend_Db_Expr('('.$sql.')')), '*');
        }
        return parent::setCollection($collection);
    }
    

    protected function _prepareColumns() {        
        if (Mage::helper('mageworx_customercredit')->isEnabled() && Mage::helper('mageworx_customercredit')->isEnabledCustomerBalanceGridColumn()) {
            $this->addColumnAfter('credit_value', array(
                //'renderer'  => 'mageworx/tweaks_adminhtml_sales_order_grid_renderer_products',
                'header' => Mage::helper('mageworx_customercredit')->__('Credit Balance'),
                'index' => 'credit_value',
                'width' => '100px',
                'type'  => 'currency',
                'renderer'  => 'mageworx_customercredit/adminhtml_widget_grid_column_renderer_currency',
                ), 'group');
        }    
        return parent::_prepareColumns();
    }
    
    public function getCurrentCurrencyCode()
    {
        if (is_null($this->_currentCurrencyCode)) {
            $this->_currentCurrencyCode = (count($this->_storeIds) > 0)
                ? Mage::app()->getStore(array_shift($this->_storeIds))->getBaseCurrencyCode()
                : Mage::app()->getStore()->getBaseCurrencyCode();
        }
        return $this->_currentCurrencyCode;
    }
}
