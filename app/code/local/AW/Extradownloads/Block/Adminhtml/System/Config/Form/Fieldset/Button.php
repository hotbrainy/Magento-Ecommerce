<?php

/**
 * Button block
 */
class AW_Extradownloads_Block_Adminhtml_System_Config_Form_Fieldset_Button extends Mage_Core_Block_Template
{
    /**
     * Default button template
     */
    const DEFAULT_BUTTON_TEMPLATE = "extradownloads/fieldset/button.phtml";

    /**
     * This is constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate(self::DEFAULT_BUTTON_TEMPLATE);
    }

    /**
     * Retrves ajax url for reset all Extra Downloads Statistics
     * 
     * @return string
     */
    public function getResetAllUrl()
    {               
        return Mage::getModel('adminhtml/url')->getUrl('adminhtml/awextradownloads_file/resetAll', array('_secure' => true));
    }


}

