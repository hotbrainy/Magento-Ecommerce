<?php
/**
 * Custom Publisher Models
 * 
 * Add custom model types, such as author, which can be used as a product
 * attribute while proviting additional details.
 * 
 * @license 	http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 * @copyright	Steven Brown March 12, 2016
 * @author		Steven Brown <steveb.27@outlook.com>
 */

class SteveB27_Publish_Block_Adminhtml_Author_Helper_Image extends Varien_Data_Form_Element_Image
{
    protected function _getUrl(){
        $url = false;
        if ($this->getValue()) {
            $url = Mage::helper('publish/image')->getImageBaseUrl().$this->getValue();
        }
        return $url;
    }
}