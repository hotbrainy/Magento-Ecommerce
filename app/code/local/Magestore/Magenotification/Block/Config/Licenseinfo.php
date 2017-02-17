<?php
/**
 * License Info Block
 */
class Magestore_Magenotification_Block_Config_Licenseinfo extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('magenotification/license/licenseinfo.phtml');
    }
    
    public function getConfigLicenseSerial()
    {
        $configData = $this->getConfigData();
        $moduleName = $this->getExtensionName();
        
        $path = 'magenotificationcerts/extension_serials/'.$moduleName;
        return isset($configData[$path]) ? $configData[$path] : '';
    }
    
    /**
     * get license serial (provided by Magestore.com)
     * 
     * @return string
     */
    public function getLicenseSerial()
    {
        $moduleConfig = $this->getModuleConfig();
        return (string)$moduleConfig->serial;
    }
    
    public function getLicenseType()
    {
        $moduleConfig = $this->getModuleConfig();
        return (string)$moduleConfig->type;
    }
    
    public function getActivationDate()
    {
        $moduleConfig = $this->getModuleConfig();
        return (string)$moduleConfig->activation_date;
    }
    
    public function getExpirationDate()
    {
        $moduleConfig = $this->getModuleConfig();
        return (string)$moduleConfig->expiration_date;
    }
}
