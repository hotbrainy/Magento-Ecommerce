<?php
/**
 * @category  Apptrian
 * @package   Apptrian_ImageOptimizer
 * @author    Apptrian
 * @copyright Copyright (c) 2016 Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Apptrian_ImageOptimizer_Block_Adminhtml_Button_Clear
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Retrieve element HTML markup
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(
        Varien_Data_Form_Element_Abstract $element
    )
    {
        $element = null;
        
        $buttonLabel = Mage::helper('apptrian_imageoptimizer')
            ->__('Clear Index');
        
        $url = Mage::helper('adminhtml')->getUrl(
            'adminhtml/apptrian_imgopt/clear'
        );
        
        $confirmText = Mage::helper('apptrian_imageoptimizer')
            ->__('Are you sure you want to do this?');
        
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            //->setId('apptrian_imageoptimizer_general_clear')
            ->setClass('apptrian-imageoptimizer-admin-button-clear')
            ->setLabel($buttonLabel)
            ->setOnClick("confirmSetLocation('".$confirmText."', '".$url."')")
            ->toHtml();
            
        return $html;
        
    }
}
