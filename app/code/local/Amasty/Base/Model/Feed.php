<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */  
class Amasty_Base_Model_Feed extends Mage_AdminNotification_Model_Feed
{
    const XML_FREQUENCY_PATH    = 'ambase/feed/check_frequency';
    const XML_LAST_UPDATE_PATH  = 'ambase/feed/last_update';
    
    const URL_NEWS        = 'http://amasty.com/feed-news.xml';
    

    public function check()
    {
        $this->checkUpdate();
    }
    
    protected function _isPromoSubscribed()
    {
        return Mage::helper("ambase/promo")->isSubscribed();
    }
    
    public function checkUpdate()
    {
        
        if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
            return $this;
        }
        
        $this->setLastUpdate();
        
        if (!extension_loaded('curl')) {
            return $this;
        }

        if ($this->_isPromoSubscribed()) {
            // load all new and relevant updates into inbox
            $feedData   = array();
            $feedXml = $this->getFeedData();
            $wasInstalled = gmdate('Y-m-d H:i:s', Amasty_Base_Helper_Module::baseModuleInstalled());

            if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
                foreach ($feedXml->channel->item as $item) {

                    $date = $this->getDate((string)$item->pubDate);

                    // compare strings, but they are well-formmatted 
                    if ($date < $wasInstalled){
                        continue;
                    }

                    $feedData[] = array(
                        'severity'      => 3,
                        'date_added'    => $this->getDate($date),
                        'title'         => (string)$item->title,
                        'description'   => (string)$item->description,
                        'url'           => (string)$item->link,
                    );
                }

                if ($feedData) {
                    $inbox = Mage::getModel('adminnotification/inbox');

                    if ($inbox)
                        $inbox->parse($feedData);   
                }
            }
        }
        
        //load all available extensions in the cache
        Amasty_Base_Helper_Module::reload();
        
        return $this;
    }

    public function getFrequency()
    {
        return Mage::getStoreConfig(self::XML_FREQUENCY_PATH);
    }

    public function getLastUpdate()
    {
        return Mage::app()->loadCache('ambase_notifications_lastcheck');
    }
 
    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'ambase_notifications_lastcheck');
        return $this;
    }
    
    public function getFeedUrl()
    {
        if (is_null($this->_feedUrl)) {
            $this->_feedUrl = self::URL_NEWS;
        }
        $query = '?s=' . urlencode(Mage::getStoreConfig('web/unsecure/base_url')); 
        return $this->_feedUrl  . $query;
    }
    
    protected function isExtensionInstalled($code)
    {
        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        foreach ($modules as $moduleName) {
            if ($moduleName == $code){
                return true;
            }
        }
        
        return false;
    }
    
}