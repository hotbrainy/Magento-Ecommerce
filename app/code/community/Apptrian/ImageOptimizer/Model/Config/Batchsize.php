<?php
/**
 * @category  Apptrian
 * @package   Apptrian_ImageOptimizer
 * @author    Apptrian
 * @copyright Copyright (c) 2016 Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Apptrian_ImageOptimizer_Model_Config_Batchsize
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
        
        $errors = array();
        $helper = Mage::helper('apptrian_imageoptimizer');
        $value  = $this->getValue();
        
        if (!Zend_Validate::is($value, 'Digits')) {
            $errors[] = $helper->__('Batch size must be an integer.');
        }
        
        if (!Zend_Validate::is($value, 'GreaterThan', array(0))) {
            $errors[] = $helper->__('Batch size must be greater than 0.');
        }
        
        if (empty($errors)) {
            return true;
        }
        
        return $errors;
        
    }
    
}
