<?php
/**
 * @category  Apptrian
 * @package   Apptrian_ImageOptimizer
 * @author    Apptrian
 * @copyright Copyright (c) 2016 Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Apptrian_ImageOptimizer_Adminhtml_Apptrian_ImgoptController
    extends Mage_Adminhtml_Controller_Action
{
    
    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('system/config/apptrian_imageoptimizer');
    }
    
    /**
     * Scan and reindex action.
     */
    public function scanAction()
    {
        
        $helper = Mage::helper('apptrian_imageoptimizer');
        
        try {
            
            $helper->scanAndReindex();
            
            $message = $this
                ->__('Scan and reindex operations completed successfully.');
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
            
        } catch (Exception $e) {
            
            $message = $this->__('Scanning and reindexing failed.');
            Mage::getSingleton('adminhtml/session')->addError($message);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            
        }
        
        $url = Mage::helper('adminhtml')->getUrl(
            'adminhtml/system_config/edit/section/apptrian_imageoptimizer'
        );
        Mage::app()->getResponse()->setRedirect($url);
        
    }
    
    /**
     * Optimize action.
     */
    public function optimizeAction()
    {
    
        $helper = Mage::helper('apptrian_imageoptimizer');
        
        if ($helper->isExecFunctionEnabled()) {
        
            try {
                
                $helper->optimize();
                
                $message = $this
                    ->__('Optimization operations completed successfully.');
                Mage::getSingleton('adminhtml/session')->addSuccess($message);
                
            } catch (Exception $e) {
                
                $message = $this->__('Optimization failed.');
                Mage::getSingleton('adminhtml/session')->addError($message);
                Mage::getSingleton('adminhtml/session')
                    ->addError($e->getMessage());
                
            }
        
        } else {
            
            $message = $this->__(
                'Optimization failed because PHP exec() function is disabled.'
            );
            Mage::getSingleton('adminhtml/session')->addError($message);
        
        }
        
        $url = Mage::helper('adminhtml')->getUrl(
            'adminhtml/system_config/edit/section/apptrian_imageoptimizer'
        );
        Mage::app()->getResponse()->setRedirect($url);
    
    }
    
    /**
     * Clear index action.
     */
    public function clearAction()
    {
        
        $helper = Mage::helper('apptrian_imageoptimizer');
        
        try {
            
            $helper->clearIndex();
            
            $message = $this
                ->__('Clear index operation completed successfully.');
            Mage::getSingleton('adminhtml/session')->addSuccess($message);
            
        } catch (Exception $e) {
            
            $message = $this->__('Clear index operation failed.');
            Mage::getSingleton('adminhtml/session')->addError($message);
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            
        }
        
        $url = Mage::helper('adminhtml')->getUrl(
            'adminhtml/system_config/edit/section/apptrian_imageoptimizer'
        );
        Mage::app()->getResponse()->setRedirect($url);
        
    }
    
}
