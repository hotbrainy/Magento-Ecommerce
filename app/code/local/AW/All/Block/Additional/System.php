<?php
class AW_All_Block_Additional_System extends Mage_Adminhtml_Block_Abstract
{
    const SUCCESS_RESULT = '<span class="available">%s</span>';
    const ERROR_RESULT   = '<span class="error">%s</span>';

    public function getTemplate()
    {
        return 'aw_all/additional_system.phtml';
    }

    public function getHtmlId()
    {
        return 'system_plugin';
    }

    public function getHeaderText()
    {
        return $this->__('System Info');
    }

    public function getPhpInfo($option)
    {
        $result = ini_get($option);
        return $this->_validate($result, $option);
    }

    public function getMagentoRequirementsUrl()
    {
        return 'http://www.magentocommerce.com/system-requirements';
    }

    public function getSystemInfo()
    {
        $result = php_uname();
        return $this->_validate($result, 'OS');
    }

    protected function _validate($resultString, $option)
    {
        $result = $resultString;
        switch ($option) {
            case 'memory_limit' :
                $result = sprintf(self::ERROR_RESULT, $resultString)
                    . sprintf(self::SUCCESS_RESULT, ' (Recommended 512M)')
                ;
                if ((int)$resultString >= 512) {
                    $result = sprintf(self::SUCCESS_RESULT, $resultString);
                }
                break;
            case 'max_execution_time' :
                $result = sprintf(self::ERROR_RESULT, $resultString)
                    . sprintf(self::SUCCESS_RESULT, ' (Recommended 1800)')
                ;
                if ((int)$resultString >= 1800) {
                    $result = sprintf(self::SUCCESS_RESULT, $resultString);
                }
                break;
            case 'OS' :
                $result = sprintf(self::ERROR_RESULT, $resultString)
                    . sprintf(self::SUCCESS_RESULT, ' (Recommended Unix OS)')
                ;
                if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                    $result = sprintf(self::SUCCESS_RESULT, $resultString);
                }
                break;
        }
        return $result;
    }
}