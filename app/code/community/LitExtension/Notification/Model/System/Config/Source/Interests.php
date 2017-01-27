<?php
/**
 * @project     Notification
 * @package	    LitExtension_Notification
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_Notification_Model_System_Config_Source_Interests
{

    const TYPE_UPDATE_RELEASE = 'UPDATE_RELEASE';
    const TYPE_INSTALLED_UPDATE = 'INSTALLED_UPDATE';
    const TYPE_NEW_RELEASE = 'NEW_RELEASE';
    const TYPE_PROMO = 'PROMO';
    const TYPE_INFO = 'INFO';

    public function toOptionArray() {
        $helper = Mage::helper('le_notification');
        return array(
            array('value' => self::TYPE_UPDATE_RELEASE, 'label' => $helper->__('All extensions updates')),
            array('value' => self::TYPE_INSTALLED_UPDATE, 'label' => $helper->__('My extensions updates')),
            array('value' => self::TYPE_NEW_RELEASE, 'label' => $helper->__('New Releases')),
            array('value' => self::TYPE_PROMO, 'label' => $helper->__('Promotions/Discounts')),
            array('value' => self::TYPE_INFO, 'label' => $helper->__('Other information'))
        );
    }
}