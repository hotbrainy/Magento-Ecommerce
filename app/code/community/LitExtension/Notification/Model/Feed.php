<?php
/**
 * @project     Notification
 * @package	    LitExtension_Notification
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_Notification_Model_Feed extends Mage_AdminNotification_Model_Feed{

    const XML_USE_HTTPS_PATH    = 'le_extension/feed/use_https';
    const XML_FEED_URL_PATH     = 'le_extension/feed/feed_url';
    const XML_FREQUENCY_PATH    = 'le_extension/feed/frequency';
    const XML_LAST_UPDATE_PATH  = 'le_extension/feed/last_update';
    const XML_INTERESTS          = 'le_extension/feed/interests';

    public static function check() {
        return Mage::getModel('le_notification/feed')->checkUpdate();
    }

    public function getFeedUrl() {
        if (is_null($this->_feedUrl)) {
            $this->_feedUrl = (Mage::getStoreConfigFlag(self::XML_USE_HTTPS_PATH) ? 'https://' : 'http://')
                . Mage::getStoreConfig(self::XML_FEED_URL_PATH);
        }
        return $this->_feedUrl;
    }

    public function getFrequency()
    {
        return Mage::getStoreConfig(self::XML_FREQUENCY_PATH)* 3600;
    }

    public function getLastUpdate()
    {
        return Mage::app()->loadCache('le_notifications_lastcheck');
    }

    public function setLastUpdate() {
        Mage::app()->saveCache(time(), 'le_notifications_lastcheck');
        return $this;
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

        $feedData = array();
        try {
            $feedXml = $this->getFeedDataChoose();
//            $feedXml = $this->getFeedData();
            if ($feedXml) {
                foreach ($feedXml->children() as $item) {

                    if (!$this->isInteresting($item)) {
                        continue;
                    }

                    $feedData[] = array(
                        'severity' => 3,
                        'date_added' => Mage::getSingleton('core/date')->gmtDate(),
                        'title' => (string) $item->title,
                        'description' => (string) $item->description,
                        'url' => (string) $item->link,
                    );
                }
                if ($feedData) {
                    Mage::getModel('adminnotification/inbox')->parse($feedData);
                }
            }
            return $this;
        } catch(Exception $e){
            return false;
        }
    }

    protected function getInterests()
    {
        return Mage::getStoreConfig(self::XML_INTERESTS);
    }

    protected function isInteresting($item)
    {
        $interests = @explode(',', $this->getInterests());
        $types = @explode(',', (string) $item->type);
        $codes = @explode(',', (string) $item->extension_code);

        $selfUpgrades = array_search(LitExtension_Notification_Model_System_Config_Source_Interests::TYPE_INSTALLED_UPDATE, $interests);

        foreach ($types as $type) {
            if (array_search($type, $interests) !== false) {
                return true;
            }

            if ($extenion && ($type == LitExtension_Notification_Model_System_Config_Source_Interests::TYPE_UPDATE_RELEASE) && $selfUpgrades) {
                if ($this->isExtensionInstalled($codes)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function isExtensionInstalled($codes)
    {
        $modules = array_keys((array) Mage::getConfig()->getNode('modules')->children());
        foreach($codes as $code){
            foreach ($modules as $moduleName) {
                if ($moduleName == $code) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function _getXmlFromUrl(){
        $response_xml_data = file_get_contents($this->getFeedUrl());
        if($response_xml_data){
            libxml_use_internal_errors(true);
            $data = simplexml_load_string($response_xml_data);
            if (!$data) {
                return false;
            } else {
                return $data;
            }
        } else {
            return false;
        }
    }

    protected function getFeedDataChoose(){
        if($this->getFeedData()){
            return $this->getFeedData();
        } else {
            return $this->_getXmlFromUrl();
        }
    }
}