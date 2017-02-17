<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */  
class Amasty_Base_Helper_Promo extends Mage_Core_Helper_Abstract
{
    function getNotificationsCollection()
    {
        $collection = Mage::getModel("adminnotification/inbox")->getCollection();
        
        $collection->getSelect()
            ->where('title like "%amasty%" or description like "%amasty%" or url like "%amasty%"')
            ->where('is_read != 1')
            ->where('is_remove != 1');
            
        return $collection;
    }
    
    function isSubscribed()
    {
        return Mage::getStoreConfig('ambase/feed/promo') == 1;
    }
}

?>