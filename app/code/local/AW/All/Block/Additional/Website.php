<?php
class AW_All_Block_Additional_Website extends Mage_Adminhtml_Block_Abstract
{
    const SUCCESS_RESULT = '<span class="available">%s</span>';
    const ERROR_RESULT   = '<span class="error">%s</span>';

    protected $_rewrites = null;

    public function getHeaderText()
    {
        return $this->__('Website Info');
    }

    public function getHtmlId()
    {
        return 'website_plugin';
    }

    public function getTemplate()
    {
        return 'aw_all/additional_website.phtml';
    }

    public function getMagentoCronStatusToHtml($fieldName = 'scheduled_at')
    {
        $scheduleCollection = Mage::getModel('cron/schedule')->getCollection();
        $scheduleCollection->setOrder($fieldName);
        $scheduleModel = $scheduleCollection->getFirstItem();

        $result = sprintf(self::ERROR_RESULT, $this->__('Never'));

        if ($scheduleModel->getId()
            && $scheduleModel->getData($fieldName) != '0000-00-00 00:00:00'
        ) {
            $findDate = new Zend_Date($scheduleModel->getData($fieldName), Varien_Date::DATETIME_INTERNAL_FORMAT);
            $today = new Zend_Date(null, Zend_Date::DATE_SHORT);

            $result = sprintf(self::SUCCESS_RESULT, $findDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            if ($findDate->compare($today, Zend_Date::DATE_SHORT) < 0) {
                $result = sprintf(self::ERROR_RESULT, $findDate->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            }
        }
        return $result;
    }

    public function getCompilationStatusToHtml()
    {
        $result = sprintf(self::SUCCESS_RESULT, $this->__('Disabled'));
        if (defined('COMPILER_INCLUDE_PATH')) {
            $result = sprintf(self::ERROR_RESULT, $this->__('Enabled'));
        }
        return $result;
    }

    public function getRewrites()
    {
        if (null === $this->_rewrites) {
            $result = array();
            foreach ($this->getModulesArray() as $moduleName => $options) {
                if (!array_key_exists('codePool', $options)) {
                    continue;
                }

                $_moduleConfigFilePath = Mage::getConfig()->getModuleDir('etc', $moduleName) . DS . 'config.xml';
                if (!file_exists($_moduleConfigFilePath)) {
                    continue;
                }
                $_moduleConfigFile = file_get_contents($_moduleConfigFilePath);
                $_configXml = new DOMDocument();
                $_configXml->loadXML($_moduleConfigFile);

                $result = $this->_getRewritesFromConfigFile($_configXml, $result);
            }

            foreach ($result as $rewritedPath => $_rewrites) {

                if (count($_rewrites) < 2) {
                    unset($result[$rewritedPath]);
                    continue;
                }

                $_classNames = array('aw' => array(), 'other' => array());
                foreach ($_rewrites as $_rewriteClass) {
                    $_modulePath = explode('_', $_rewriteClass);
                    $_usedClass = Mage::getConfig()->getGroupedClassName(strtolower($_modulePath[2]), $rewritedPath);

                    $_rewriteClassHtml = sprintf(self::ERROR_RESULT, $_rewriteClass);
                    if ($_usedClass == $_rewriteClass) {
                        $_rewriteClassHtml = sprintf(self::SUCCESS_RESULT, $_rewriteClass);
                    }

                    if ($this->isAWModule($_rewriteClass)) {
                        $_classNames['aw'][] = $_rewriteClassHtml;
                    } else {
                        $_classNames['other'][] = $_rewriteClassHtml;
                    }
                }
                unset($result[$rewritedPath]);
                array_push($result, $_classNames);
            }
            $this->_rewrites = $result;
        }
        return $this->_rewrites;
    }

    protected function getModulesArray()
    {
        return Mage::getConfig()->getNode('modules')->asArray();
    }

    public function isAWModule($className)
    {
        $_modulePath = explode('_', $className);
        if (count($_modulePath) != 0 && $_modulePath[0] == 'AW') {
            return true;
        }
        return false;
    }

    protected function _getRewritesFromConfigFile($_configXml, $result)
    {
        $nodeTypes = array('blocks', 'models', 'helpers');
        foreach ($nodeTypes as $nodeType) {

            if (!$_configXml->documentElement) {
                continue;
            }

            foreach ($_configXml->documentElement->getElementsByTagName($nodeType) as $nodeElements) {
                foreach ($nodeElements->getElementsByTagName('rewrite') as $nodeAttribute) {
                    $moduleName = $nodeAttribute->parentNode->tagName;

                    foreach ($nodeAttribute->getElementsByTagName('*') as $childNode) {
                        $rewriteClass = $childNode->nodeValue;
                        $key = $moduleName . DS . $childNode->tagName;

                        if (!isset($result[$key])) {
                            $result[$key] = array();
                        }
                        $result[$key][] = $rewriteClass;
                    }
                }
            }
        }
        return $result;
    }

    public function getWebsiteThemesParams()
    {
        $result = array();
        $storeCollection = Mage::getModel('core/store')->getCollection();
        foreach ($storeCollection as $storeModel) {
            $storeDesignModel = Mage::getModel('core/design_package')->setStore($storeModel);
            $_designRules = Mage::getModel('core/design')->loadChange($storeModel->getId());

            $_packageName = $storeDesignModel->getPackageName();
            if (null !== $_designRules->getPackage() && $_designRules->getPackage() != $_packageName) {
                $_packageName = $_designRules->getPackage();
            }

            $_templatesPath = $storeDesignModel->getTheme('template');
            if (null !== $_designRules->getTheme() && $_designRules->getTheme() != $_templatesPath) {
                $_templatesPath = $_designRules->getTheme();
            }

            $_storeDesign = array(
                'store'        => $storeModel->getWebsite()->getName() . DS . $storeModel->getName(),
                'package'      => $_packageName,
                'templates'    => $_templatesPath,
                'skin'         => $storeDesignModel->getTheme('skin'),
                'layout'       => $storeDesignModel->getTheme('layout')
            );
            array_push($result, $_storeDesign);
        }
        return $result;
    }

    public function getDisabledAWModules()
    {
        $result = array();
        foreach ($this->getModulesArray() as $moduleName => $options) {
            if (!$this->isAWModule($moduleName)) {
                continue;
            }

            $_source = array();
            if (!array_key_exists('active', $options) || !in_array($options['active'], array('true', '1'))) {
                array_push($_source, 'xml');
            }

            if (Mage::getStoreConfigFlag('advanced/modules_disable_output/' . $moduleName)) {
                array_push($_source, 'output');
            }

            if (count($_source) != 0) {
                $result[] = array('module_name' => $moduleName, 'source' => implode('/', $_source));
            }
        }
        return $result;
    }
}