<?php
class AW_All_Block_System_Config_Form_Fieldset_Awall_Additional extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);

        foreach ($element->getElements() as $field) {
            $html .= $field->toHtml();
        }

        $html .= "<tr>
            <td class=\"label\"></td>
            <td class=\"value\">
            <button class=\"scalable\" onclick=\"window.location='" . Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/awall_additional/index') . "'\" type=\"button\">
                <span>View Additional info</span>
            </button
            </td>
         </tr>
         ";
        $html .= $this->_getFooterHtml($element);

        return $html;
    }
}