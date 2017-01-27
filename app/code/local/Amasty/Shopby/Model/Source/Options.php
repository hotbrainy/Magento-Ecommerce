<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
class Amasty_Shopby_Model_Source_Options extends Varien_Object
{
    public function toOptionArray()
    {
        $hlp = Mage::helper('amshopby');
        return array(
        	array('value' => '-',   'label' => $hlp->__('-')),
        	array('value' => '_',   'label' => $hlp->__('_')),
            array('value' => '--',  'label' => $hlp->__('--')),
        );
    }
    
}