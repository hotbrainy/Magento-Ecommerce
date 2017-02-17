<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
class Amasty_Shopby_Model_Source_Price extends Amasty_Shopby_Model_Source_Abstract
{
    public function toOptionArray()
    {
        $hlp = Mage::helper('amshopby');
        return array(
            array('value' => Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_DEFAULT,    'label' => $hlp->__('Default')),
            array('value' => Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_DROPDOWN,   'label' => $hlp->__('Dropdown')),
            array('value' => Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_FROMTO,     'label' => $hlp->__('From-To Only')),
            array('value' => Amasty_Shopby_Model_Catalog_Layer_Filter_Price::DT_SLIDER,     'label' => $hlp->__('Slider')),
        );
    }
}