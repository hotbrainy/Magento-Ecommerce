<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Rules_Condition_Action extends MageWorx_CustomerCredit_Model_Rules_Condition_Abstract
{
    public function loadAttributeOptions() {
        $attributes = array(
//            'place_order'       => Mage::helper('mageworx_customercredit')->__('Place Order Profit'), # DEPRICATED 2.6.0 -> Use 50% in other actions
            'number_of_orders'         => Mage::helper('mageworx_customercredit')->__('Number Of Orders'),
            'order_total'         => Mage::helper('mageworx_customercredit')->__('Order Total'),
        );
        $this->setAttributeOption($attributes);
        return $this;
    }
    
    public function loadOperatorOptions() {
        $this->setOperatorOption(array(
            '=='  => Mage::helper('mageworx_customercredit')->__('is'),
            '!='  => Mage::helper('mageworx_customercredit')->__('is not'),
            '>='  => Mage::helper('mageworx_customercredit')->__('equals or greater than'),
            '>='  => Mage::helper('mageworx_customercredit')->__('equals or greater than'),
            '<='  => Mage::helper('mageworx_customercredit')->__('equals or less than'),
            '>'   => Mage::helper('mageworx_customercredit')->__('greater than'),
            '<'   => Mage::helper('mageworx_customercredit')->__('less than')
        ));
        $this->setOperatorByInputType(array(
            'string' => array('==', '>='),
            'number_of_orders' => array('==', '!=', '>=', '<=', '>', '<'),
            'order_total' => array('==', '!=', '>=', '<=', '>', '<')
        ));
        return $this;
    }
    
   
}
