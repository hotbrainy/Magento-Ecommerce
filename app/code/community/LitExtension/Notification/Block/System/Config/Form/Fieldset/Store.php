<?php
/**
 * @project     Notification
 * @package	    LitExtension_Notification
 * @author      LitExtension
 * @email       litextension@gmail.com
 */

class LitExtension_Notification_Block_System_Config_Form_Fieldset_Store
    extends Mage_Adminhtml_Block_System_Config_Form_Fieldset{

    protected $_dummyElement;
    protected $_fieldRenderer;
    protected $_values;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return '<iframe scrolling="auto" style="width: 100%; height:1000px;" src="http://litextension.com/magento-extensions.html" id="' . $element->getId() . '"></iframe>';
        return $html;
    }

}