<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_System_Config_Source_Totals extends Mage_Core_Model_Config_Data
{
    public function toOptionArray() {
        $list = array(
            array('value'=>'subtotal', 'label'=>Mage::helper('mageworx_customercredit')->__('Subtotal')),
            array('value'=>'shipping', 'label'=>Mage::helper('mageworx_customercredit')->__('Shipping & Handling')),
            array('value'=>'tax', 'label'=>Mage::helper('mageworx_customercredit')->__('Tax'))
        );
        if(Mage::helper('core')->isModuleEnabled('MageWorx_MultiFees')) {
            array_push($list, array('value'=>'fees', 'label'=>Mage::helper('mageworx_customercredit')->__('Fees')));
        }
        return $list;
    }            
    
}