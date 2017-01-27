<?php
class AW_All_Block_Jsinit extends Mage_Adminhtml_Block_Template
{
    protected $_platform = -1;
    protected $_extensions_cache = array();
    protected $_extensions;

    protected $_section = '';

    /**
     * Include JS in head if section is moneybookers
     */
    protected function _prepareLayout()
    {
        $this->_section = $this->getAction()->getRequest()->getParam('section', false);
        if ($this->_section == 'awall') {
            $this->getLayout()
                    ->getBlock('head')
                    ->addJs('aw_all/aw_all.js');
            $this->setData('extensions', $this->_initExtensions());
        }
        parent::_prepareLayout();
    }

    /**
     * Print init JS script into body
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_section == 'awall') {
            return parent::_toHtml();
        } else {
            return '';
        }
    }

    protected function _initExtensions()
    {

        $extensions = array();

        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
        sort($modules);

        foreach ($modules as $moduleName) {
            if (strstr($moduleName, 'AW_') === false) {
                continue;
            }

            if ($moduleName == 'AW_Core' || $moduleName == 'AW_All') {
                continue;
            }
            // Detect extension platform
            try {
                if ($platform = Mage::getConfig()->getNode("modules/$moduleName/platform")) {
                    $platform = strtolower($platform);
                    $ignore_platform = false;
                } else {
                    throw new Exception();
                }
            } catch (Exception $e) {
                $platform = "ce";
                $ignore_platform = true;
            }
            $platform = AW_All_Helper_Versions::convertPlatform($platform);

            // Detect installed version
            $ver = (Mage::getConfig()->getModuleConfig($moduleName)->version);
            if ((bool)$ver === false) {
                $_moduleConfigFilePath = Mage::getConfig()->getModuleDir('etc', $moduleName) . DS . 'config.xml';
                if (file_exists($_moduleConfigFilePath)) {
                    $_configXml = new SimpleXMLElement(file_get_contents($_moduleConfigFilePath));
                    $ver = (string)$_configXml->modules->$moduleName->version;
                }
            }
            $isPlatformValid = $platform >= $this->getPlatform();
            $feedInfo = $this->getExtensionInfo($moduleName);
            $upgradeAvailable = ($this->_convertVersion($feedInfo->getLatestVersion()) - $this->_convertVersion($ver)) > 0;

            if (null !== $feedInfo->getDisplayName()) {
                $moduleName = $feedInfo->getDisplayName();
            }

            $extensions[] = new Varien_Object(
                array(
                    'version'           => $ver,
                    'name'              => $moduleName,
                    'is_platform_valid' => $isPlatformValid,
                    'platform'          => $platform,
                    'feed_info'         => $feedInfo,
                    'upgrade_available' => $upgradeAvailable
                )
            );
        }
        return $extensions;
    }

    /**
     * Convert version to comparable integer
     * @param $v
     * @return int
     */
    protected function _convertVersion($v)
    {
        $digits = @explode(".", $v);
        $version = 0;
        if (is_array($digits)) {
            foreach ($digits as $k => $v) {
                $version += ($v * pow(10, max(0, (3 - $k))));
            }
        }
        return $version;
    }


    /**
     * Get extension info from cached feed
     * @param $moduleName
     * @return bool|Varien_Object
     */
    public function getExtensionInfo($moduleName)
    {
        if (!sizeof($this->_extensions_cache)) {
            if ($displayNames = Mage::app()->loadCache('aw_all_extensions_feed')) {
                $this->_extensions_cache = @unserialize($displayNames);
            }
        }
        if (array_key_exists($moduleName, $this->_extensions_cache)) {
            $data = array(
                'url' => @$this->_extensions_cache[$moduleName]['url'],
                'display_name' => @$this->_extensions_cache[$moduleName]['display_name'],
                'latest_version' => @$this->_extensions_cache[$moduleName]['version'],
                'documentation_url' => @$this->_extensions_cache[$moduleName]['documentation_url'],
            );
            return new Varien_Object($data);
        }
        return new Varien_Object();
    }

    /**
     * Return icon for installed extension
     * @param $Extension
     * @return Varien_Object
     */
    public function getIcon($Extension)
    {
        if ($Extension->getUpgradeAvailable()) {
            $icon = 'aw_all/images/update.gif';
            $title = "Update available";
        } elseif (!$Extension->getIsPlatformValid()) {
            $icon = 'aw_all/images/bad.gif';
            $title = "Wrong Extension Platform";
        } else {
            $icon = 'aw_all/images/ok.gif';
            $title = "Extension is up-to-date";
        }
        return new Varien_Object(array('title' => $title, 'source' => $this->getSkinUrl($icon)));
    }

}
 
