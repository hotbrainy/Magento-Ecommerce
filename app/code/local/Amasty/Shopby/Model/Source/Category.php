<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
class Amasty_Shopby_Model_Source_Category extends Varien_Object
{
    public function toOptionArray()
    {
        $hlp = Mage::helper('amshopby');
        return array(
            array('value' => Amasty_Shopby_Model_Catalog_Layer_Filter_Category::DT_DEFAULT,     'label' => $hlp->__('Default')),
            array('value' => Amasty_Shopby_Model_Catalog_Layer_Filter_Category::DT_DROPDOWN,    'label' => $hlp->__('Dropdown')),
            array('value' => Amasty_Shopby_Model_Catalog_Layer_Filter_Category::DT_WSUBCAT,     'label' => $hlp->__('With Sub-Categories')),
            array('value' => Amasty_Shopby_Model_Catalog_Layer_Filter_Category::DT_STATIC2LVL,  'label' => $hlp->__('Static 2-Level Tree')),
            array('value' => Amasty_Shopby_Model_Catalog_Layer_Filter_Category::DT_ADVANCED,    'label' => $hlp->__('Advanced Categories')),
        );
    }
}