<?php
class OE_SocialShare_Adminhtml_Model_System_Config_Source_Color
{
     public function toOptionArray()
    {
        return array(
            array('value'=>'light', 'label'=>Mage::helper('socialshare')->__('Light')),
            array('value'=>'dark', 'label'=>Mage::helper('socialshare')->__('Dark'))
        );
    }
}
