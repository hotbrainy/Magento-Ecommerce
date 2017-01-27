<?php

class Idev_OneStepCheckout_Block_Adminhtml_Version extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    protected $_fieldRenderer;

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = '
            <tr>
                <td class="label"><label for="'.$element->getHtmlId().'">'.$element->getLabel().'</label></td>
                <td class="value" id="version_info">'.Mage::getConfig()->getNode('modules/Idev_OneStepCheckout')->version.'</td>
            </tr>
        ';

        return $html;
    }

    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('adminhtml/system_config_form_field');
        }

        return $this->_fieldRenderer;
    }

}
