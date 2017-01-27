<?php
/**
 * @category  Apptrian
 * @package   Apptrian_ImageOptimizer
 * @author    Apptrian
 * @copyright Copyright (c) 2016 Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Apptrian_ImageOptimizer_Model_Config_Cron_Scan
    extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH = 
        'crontab/jobs/apptrian_imageoptimizer_scan/schedule/cron_expr';
    
    protected function _afterSave()
    {
        
        $cronExprString = $this->getValue();
        
        try {
            
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
            
        } catch (Exception $e) {
            
            throw new Exception(
                Mage::helper('cron')->__('Unable to save the cron expression.')
            );
            
        }
        
    }
    
    public function _beforeSave()
    {
        
        $result = $this->validate();
        
        if ($result !== true) {
            
            Mage::throwException(implode("\n", $result));
            
        }
    
        return parent::_beforeSave();
    
    }
    
    public function validate()
    {
        
        $errors    = array();
        $helper    = Mage::helper('apptrian_imageoptimizer');
        $value     = $this->getValue();
        $validator = Zend_Validate::is(
            $value, 'Regex', array('pattern' => '/^[0-9,\-\?\/\*\ ]+$/')
        );
        
        if (!$validator) {
            $errors[] = $helper->__('Cron expression is invalid.');
        }
        
        if (empty($errors)) {
            return true;
        }
        
        return $errors;
        
    }
    
}
