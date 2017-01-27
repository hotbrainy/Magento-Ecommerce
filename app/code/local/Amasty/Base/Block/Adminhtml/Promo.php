<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */ 
class Amasty_Base_Block_Adminhtml_Promo extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_promoHelper;
    
    protected function _getPromoHelper()
    {
        if (!$this->_promoHelper)
        {
            $this->_promoHelper = Mage::helper("ambase/promo");
        }
        
        return $this->_promoHelper;
    }
    
    function getLatestNotification()
    {
        $ret = null;
        
        $mageNotifications = !Mage::getStoreConfig('advanced/modules_disable_output/Mage_AdminNotification');
        
        $collection = $this->_getPromoHelper()->getNotificationsCollection();
        
        $collection->getSelect()
            
            ->order('notification_id DESC')
            ->limit(1);
        
        if ($this->isSubscribed() && !$mageNotifications)
        {
            $items = array_values($collection->getItems());
        
            $ret = count($items) > 0 ? $items[0] : null;
        }
        
        return $ret;
    }
    
    function getCloseUrl()
    {
        return Mage::helper("adminhtml")->getUrl("adminhtml/ambase_base/closePromo", array(
        ));
    }
    
    function getUnsubscribeUrl()
    {
        return Mage::helper("adminhtml")->getUrl("adminhtml/system_config/edit/section/ambase", array(
        
        ));
    }
    
    function isSubscribed()
    {
        return $this->_getPromoHelper()->isSubscribed();
    }
    
}