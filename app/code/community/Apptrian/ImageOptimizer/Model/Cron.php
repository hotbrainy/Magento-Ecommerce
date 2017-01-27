<?php
/**
 * @category  Apptrian
 * @package   Apptrian_ImageOptimizer
 * @author    Apptrian
 * @copyright Copyright (c) 2016 Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Apptrian_ImageOptimizer_Model_Cron
{
    
    /**
     * Cron method for executing scan and reindex process.
     */
    public function scan()
    {
        
        $helper           = Mage::helper('apptrian_imageoptimizer');
        
        $extensionEnabled = (int) $helper->getConfig(
            'apptrian_imageoptimizer/general/enabled'
        );
        
        $cronJobEnabled   = (int) $helper->getConfig(
            'apptrian_imageoptimizer/cron/enabled_scan'
        );
            
        if ($extensionEnabled && $cronJobEnabled) {
            
            try {
                
                $result = $helper->scanAndReindex();
                
                if ($result !== true ) {
                    $mPrefix = 'Image Optimizer Cron: Scan and Reindex process';
                    Mage::log($mPrefix . ' failed.');
                }
                
            } catch (Exception $e) {
                
                Mage::log($e);
                
            }
            
        }
            
    }
    
    /**
     * Cron method for executing optmization process.
     */
    public function optimize()
    {
        
        $helper           = Mage::helper('apptrian_imageoptimizer');
        
        $extensionEnabled = (int) $helper->getConfig(
            'apptrian_imageoptimizer/general/enabled'
        );
        
        $cronJobEnabled   = (int) $helper->getConfig(
            'apptrian_imageoptimizer/cron/enabled_optimize'
        );
        
        if ($extensionEnabled && $cronJobEnabled) {
        
            $mPrefix = 'Image Optimizer Cron: Optimization process ';
        
            if ($helper->isExecFunctionEnabled()) {
        
                try {
        
                    $result = $helper->optimize();
        
                    if ($result !== true ) {
                        Mage::log($mPrefix . 'failed.');
                    }
        
                } catch (Exception $e) {
        
                    Mage::log($e);
        
                }
        
            } else {
        
                Mage::log(
                    $mPrefix . 'failed because PHP exec() function is disabled.'
                );
        
            }
        
        }
        
    }
    
}
