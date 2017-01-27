<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */  
class Amasty_Base_Helper_Module extends Mage_Core_Helper_Abstract
{
    const INSTALLED_PATH = 'ambase/feed/installed';
    const EXTENSIONS_PATH = 'ambase_extensions';
    const UPDATED_PREFIX = 'ambase/feed/updated_';
    
    const URL_EXTENSIONS  = 'http://amasty.com/feed-extensions.xml';
    const BASE_MODULE_PERIOD = 3;
    const MODULE_PERIOD = 1;
    
    protected $_controllerModule;
    protected $_extension = array(
        'name'    => null,
        'url'     => null,
        'version' => null
    );
    
    
    function init($controllerModule)
    {
        $segments = explode('_', $controllerModule);
        $controllerModule = implode('_', array_slice($segments, 0, 2));
        $this->_controllerModule = $controllerModule;
        return $this;
    }
    
    static function reload()
    {
        $feedData   = array();
        $feedXml = self::getFeedData();
        if ($feedXml && $feedXml->channel && $feedXml->channel->item) 
        {
            foreach ($feedXml->channel->item as $item) {
                $code = (string)$item->code;

                if (!isset($feedData[$code])){
                    $feedData[$code] = array();
                }

                $feedData[$code][(string)$item->title] = array(
                    'name'    => (string)$item->title,
                    'url'     => (string)$item->link,
                    'version' => (string)$item->version,
                );
            }

            if ($feedData) 
            {
                Mage::app()->saveCache(serialize($feedData), self::EXTENSIONS_PATH);
            }
        }
    }
        
    
    static function getFeedData()
    {
        if (!extension_loaded('curl')) {
            return null;
        }
        
        $curl = new Varien_Http_Adapter_Curl();
        $curl->setConfig(array(
            'timeout'   => 2
        ));
        $curl->write(Zend_Http_Client::GET, self::URL_EXTENSIONS, '1.0');
        $data = $curl->read();
        if ($data === false) {
            return false;
        }
        $data = preg_split('/^\r?$/m', $data, 2);
        $data = trim($data[1]);
        $curl->close();

        try {
            $xml  = new SimpleXMLElement($data);
        }
        catch (Exception $e) {
            return false;
        }

        return $xml;
    }
    
    static function getAllExtensions()
    {
        $ret = @unserialize(Mage::app()->loadCache(self::EXTENSIONS_PATH));
        
        if (!$ret)
        {
            self::reload();
            $ret = @unserialize(Mage::app()->loadCache(self::EXTENSIONS_PATH));
        }
        
        return $ret;
    }
    
    protected function _getExtension()
    {
        if (!$this->_extension || $this->_extension['name'] === null)
        {
            $allExtensions = self::getAllExtensions();
            
            if (isset($allExtensions[$this->_controllerModule])) 
            {
                $this->_extension = array_pop($allExtensions[$this->_controllerModule]);
            }
        }
        return $this->_extension;
    }
    
    function getModuleCode()
    {
        $item = $this->_getExtension();
        return $this->_controllerModule;
    }
    
    function getModuleTitle()
    {
        $item = $this->_getExtension();
        return (string) ($item ? $item['name'] : null);
    }
    
    function getModuleLink()
    {
        $item = $this->_getExtension();
        return (string) ($item ? $item['url'] : null);
    }
    
    function getLatestVersion()
    {
        $item = $this->_getExtension();
        return (string) ($item ? $item['version'] : null);
    }
    
    protected function _getInstalledVersion()
    {
        $ret = null;
        $modules = (array)Mage::getConfig()->getNode('modules')->children();
        $moduleCode = $this->getModuleCode();
        if (isset($modules[$moduleCode]))
        {
           $ret = (string)$modules[$moduleCode]->version;
        }
        return $ret;
    }
    
    protected function _isAmastyModule()
    {
        return strpos($this->_controllerModule, "Amasty") !== FALSE && $this->_getInstalledVersion() !== NULL;
    }
    
    static function baseModuleInstalled()
    {
        $ret = Mage::getStoreConfig(Amasty_Base_Helper_Module::INSTALLED_PATH);
        if (!$ret)
        {
            $ret = time();
            Mage::getConfig()->saveConfig(Amasty_Base_Helper_Module::INSTALLED_PATH, $ret);
            Mage::getConfig()->cleanCache();
        }
        return $ret;
    }
    
    function moduleUpdated()
    {
        $path = Amasty_Base_Helper_Module::UPDATED_PREFIX . $this->_controllerModule;
        $ret = Mage::getStoreConfig($path);
        if (!$ret)
        {
            $this->setModuleUpdated();
        }
        return $ret;
    }
    
    function setModuleUpdated()
    {
        $path = Amasty_Base_Helper_Module::UPDATED_PREFIX . $this->_controllerModule;
        Mage::getConfig()->saveConfig($path, time());
        Mage::getConfig()->cleanCache();
    }
    
    protected function _validateBaseModulePeriod()
    {
        return strtotime("+" . self::BASE_MODULE_PERIOD . " month" , self::baseModuleInstalled()) < time();
    }
    
    protected function _validateModulePeriod()
    {
        return strtotime("+" . self::MODULE_PERIOD . " month" , self::moduleUpdated()) < time();
    }
    
    function isNewVersionAvailable()
    {
        $ret = false;
        if ($this->_isAmastyModule() && $this->_validateBaseModulePeriod() && $this->_validateModulePeriod())
        {
            if (version_compare($this->getLatestVersion(), $this->_getInstalledVersion(), 'gt'))
            {
                
                $ret = true;
            }
        }
        return $ret;
    }
    
    function isSubscribed()
    {
        return Mage::getStoreConfig('ambase/feed/update') == 1;
    }
}