<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Block_Adminhtml_System_Config_Frontend_Sync_Credits extends Mage_Adminhtml_Block_System_Config_Form_Field
{    
    
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {        
        $html = $element->getElementHtml();
        $this->setElement($element);
        return $html ."<br><br>".$this->_getAddRowButtonHtml($element->getValue());
    }

    protected function _getAddRowButtonHtml($type) {        
        $title = $this->__('Sync Credits');
        $buttonBlock = $this->getElement()->getForm()->getParent()->getLayout()->createBlock('adminhtml/widget_button');

        $_websiteCode = $buttonBlock->getRequest()->getParam('website');
    
        $url = Mage::helper('adminhtml')->getUrl('adminhtml/mageworx_customercredit_credit/sync/',array('sync_type'=>$type));
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setType('button')
                ->setId('sync_button')
                ->setLabel($this->__($title))
                ->setOnClick("setSync()")
                ->toHtml();
        $html .= "<script type='text/javascript'>
            function setSync() {
                var syncType = $('mageworx_customercredit_main_sync_credits').value;
                var url = '" . $url . "';
                
                return window.location.href=url+'sync_type/'+syncType+'/';
            }
        </script>
        ";
        return $html;
    }

}