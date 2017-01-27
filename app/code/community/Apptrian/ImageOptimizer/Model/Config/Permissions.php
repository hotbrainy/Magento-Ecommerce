<?php
/**
 * @category  Apptrian
 * @package   Apptrian_ImageOptimizer
 * @author    Apptrian
 * @copyright Copyright (c) 2016 Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Apptrian_ImageOptimizer_Model_Config_Permissions
    extends Mage_Core_Model_Config_Data
{
    
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
            $value, 'Regex', 
            array('pattern' => '/^[0-7]*$/')
        );
        
        if (!$validator) {
            $errors[] = $helper->__('Force File Permissions field is invalid.');
        }
        
        if (empty($errors)) {
            return true;
        }
        
        return $errors;
        
    }
    
}
