<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/LICENSE-M1.txt
 *
 * @category   AW
 * @package    AW_All
 * @version    1.0
 * @copyright  Copyright (c) 2009-2010 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
 */

class AW_All_Model_Source_Updates_Type extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    const TYPE_PROMO = 'PROMO';
    const TYPE_NEW_RELEASE = 'NEW_RELEASE';
    const TYPE_UPDATE_RELEASE = 'UPDATE_RELEASE';
    const TYPE_INFO = 'INFO';
    const TYPE_INSTALLED_UPDATE = 'INSTALLED_UPDATE';


    public function toOptionArray()
    {
        return array(
            array('value' => self::TYPE_INSTALLED_UPDATE, 'label' => Mage::helper('awall')->__('My extensions updates')),
            array('value' => self::TYPE_UPDATE_RELEASE, 'label' => Mage::helper('awall')->__('All extensions updates')),
            array('value' => self::TYPE_NEW_RELEASE, 'label' => Mage::helper('awall')->__('New Releases')),
            array('value' => self::TYPE_PROMO, 'label' => Mage::helper('awall')->__('Promotions/Discounts')),
            array('value' => self::TYPE_INFO, 'label' => Mage::helper('awall')->__('Other information'))
        );
    }

    /**
     * Retrive all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }


    /**
     * Returns label for value
     * @param string $value
     * @return string
     */
    public function getLabel($value)
    {
        $options = $this->toOptionArray();
        foreach ($options as $v) {
            if ($v['value'] == $value) {
                return $v['label'];
            }
        }
        return '';
    }

    /**
     * Returns array ready for use by grid
     * @return array
     */
    public function getGridOptions()
    {
        $items = $this->getAllOptions();
        $out = array();
        foreach ($items as $item) {
            $out[$item['value']] = $item['label'];
        }
        return $out;
    }
}
