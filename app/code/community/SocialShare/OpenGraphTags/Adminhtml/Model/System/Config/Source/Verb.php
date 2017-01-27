<?php
class OE_SocialShare_Adminhtml_Model_System_Config_Source_Verb
{
     public function toOptionArray()
    {
        return array(
            array('value'=>'like', 'label'=>Mage::helper('socialshare')->__('Like')),
            array('value'=>'recommend', 'label'=>Mage::helper('socialshare')->__('Recommend'))
        );
    }
}