<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
class Amasty_Shopby_Model_Source_Category_Start extends Varien_Object
{
    const START_ROOT = 0;
    const START_CURRENT = 1;
    const START_CHILDREN = 2;
    
    public function toOptionArray()
    {
        $hlp = Mage::helper('amshopby');
        return array(
            array('value' => self::START_ROOT,      'label' => $hlp->__('Root Category')),
            array('value' => self::START_CURRENT,   'label' => $hlp->__('Same As Current Category')),
            array('value' => self::START_CHILDREN,  'label' => $hlp->__('Current Category Children'))
        );
    }
}