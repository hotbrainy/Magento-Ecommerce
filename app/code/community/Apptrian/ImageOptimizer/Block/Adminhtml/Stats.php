<?php
/**
 * @category  Apptrian
 * @package   Apptrian_ImageOptimizer
 * @author    Apptrian
 * @copyright Copyright (c) 2016 Apptrian (http://www.apptrian.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License
 */
class Apptrian_ImageOptimizer_Block_Adminhtml_Stats
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Retrieve element HTML markup.
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(
        Varien_Data_Form_Element_Abstract $element
    )
    {
        $element   = null;
        $r         = Mage::helper('apptrian_imageoptimizer')->getFileCount();
        $indexed   = $r['indexed'];
        $optimized = $r['optimized'];
        
        // Fix for division by zero possibility
        if ($indexed == 0) {
            $percent = 0;
        } else {
            $percent = round((100 * $optimized) / $indexed, 2);
        }
        
        $html = '<div class="apptrian-imageoptimizer-bar-wrapper">
        <div class="apptrian-imageoptimizer-bar-outer">
        <div class="apptrian-imageoptimizer-bar-inner" style="width:' 
        . $percent .'%;"></div>
        <div class="apptrian-imageoptimizer-bar-text"><span>' . $percent . '% ' 
        . Mage::helper('apptrian_imageoptimizer')
            ->__('(%s of %s files)', $optimized, $indexed) . '</span></div>
        </div></div>';
        
        return $html;
        
    }
}
