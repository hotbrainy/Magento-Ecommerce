<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
class Amasty_Shopby_Model_Source_Canonical extends Varien_Object
{
    const CANONICAL_DEFAULT = -1;
    const CANONICAL_KEY = 0;
    const CANONICAL_CURRENT_URL = 1;
    const CANONICAL_FIRST_ATTRIBUTE_VALUE = 2;

    public function toOptionArray()
    {
        $hlp = Mage::helper('amshopby');
        return array(
            array('value' => self::CANONICAL_DEFAULT, 'label' => $hlp->__('Do Not Change')),
            array('value' => self::CANONICAL_KEY, 'label' => $hlp->__('Just Url Key')),
            array('value' => self::CANONICAL_CURRENT_URL, 'label' => $hlp->__('Current URL without GET Parameters')),
            array('value' => self::CANONICAL_FIRST_ATTRIBUTE_VALUE, 'label' => $hlp->__('First Attribute Value')),
        );
    }
}