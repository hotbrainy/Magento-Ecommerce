<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */  
class Amasty_Shopby_Model_Source_Attribute extends Amasty_Shopby_Model_Source_Abstract
{
    const DT_LABELS_ONLY = 0;
    const DT_IMAGES_ONLY = 1;
    const DT_IMAGES_AND_LABELS = 2;
    const DT_DROPDOWN = 3;
    const DT_LABELS_IN_2_COLUMNS = 4;
    const DT_MAGENTO_SWATCHES = 5;

    public function toOptionArray()
    {
        $hlp = Mage::helper('amshopby');

        $result = array(
            array('value' => self::DT_LABELS_ONLY, 'label' => $hlp->__('Labels Only')),
            array('value' => self::DT_IMAGES_ONLY, 'label' => $hlp->__('Images Only')),
            array('value' => self::DT_IMAGES_AND_LABELS, 'label' => $hlp->__('Images and Labels')),
            array('value' => self::DT_DROPDOWN, 'label' => $hlp->__('Drop-down List')),
            array('value' => self::DT_LABELS_IN_2_COLUMNS, 'label' => $hlp->__('Labels in 2 columns')),
        );

        if ($hlp->isModuleEnabled('Mage_ConfigurableSwatches')) {
            $result[] = array('value' => self::DT_MAGENTO_SWATCHES, 'label' => $hlp->__('Magento Swatches (or fallback to "Labels Only")'));
        }

        return $result;
    }
}