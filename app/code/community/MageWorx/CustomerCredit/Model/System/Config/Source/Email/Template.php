<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_System_Config_Source_Email_Template extends Mage_Adminhtml_Model_System_Config_Source_Email_Template
{    
    
    
    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        if(!$collection = Mage::registry('config_system_email_template')) {
            $collection = Mage::getResourceModel('core/email_template_collection')
                ->load();

            Mage::register('config_system_email_template', $collection);
        }
        $options = $collection->toOptionArray();
        $templateName = Mage::helper('mageworx_customercredit')->__('Default Template from Locale');
        $nodeName = "";
        $templateLabelNode = Mage::app()->getConfig()->getNode(self::XML_PATH_TEMPLATE_EMAIL . $nodeName . '/label');
        if ($templateLabelNode) {
            $templateName = Mage::helper('mageworx_customercredit')->__((string)$templateLabelNode);
            $templateName = Mage::helper('mageworx_customercredit')->__('%s (Default Template from Locale)', $templateName);
        }
        array_unshift(
            $options,
            array(
                'value'=> $nodeName,
                'label' => $templateName
            )
        );
        return $options;
    }

    public function getOptionArray() {
        foreach($this->toOptionArray() as $key=>$option) {
            $optionArray[$key] = $option['label'];
        }
        return $optionArray;
    }
}