<?php
class SocialShare_OpenGraphTags_Adminhtml_Model_System_Config_Source_Language
{
     public function toOptionArray()
    {
        return array(
            array('value'=>'en_US', 'label'=>Mage::helper('opengraphtags')->__('English US')),
            array('value'=>'de_DE', 'label'=>Mage::helper('opengraphtags')->__('German'))
        );
    }
}
