<?php
/**
 * @category  Apptrian
 * @package   Apptrian_ImageOptimizer
 * @author    Apptrian
 * @copyright Copyright (c) 2016 Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Apptrian_ImageOptimizer_Block_Adminhtml_Button_Scan
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
        
        $elementOriginalData = $element->getOriginalData();
        
        if (isset($elementOriginalData['label'])) {
            $buttonLabel = $elementOriginalData['label'];
        } else {
            return '<div>Button label was not specified</div>';
        }
        
        $url = Mage::helper('adminhtml')->getUrl(
            'adminhtml/apptrian_imgopt/scan'
        );
        
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            //->setId('apptrian_imageoptimizer_general_scan')
            ->setClass('apptrian-imageoptimizer-admin-button-scan')
            ->setLabel($buttonLabel)
            ->setOnClick("setLocation('$url')")
            ->toHtml();
            
        return $html;
        
    }
}
