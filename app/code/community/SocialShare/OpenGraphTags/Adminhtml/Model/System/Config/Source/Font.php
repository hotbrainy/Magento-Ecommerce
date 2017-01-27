<?php

exit("FacebookComments");


class SocialShare_FacebookComments_Adminhtml_Model_System_Config_Source_Font
{
     public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=>Mage::helper('socialshare')->__('')),
            array('value'=>'arial', 'label'=>Mage::helper('socialshare')->__('arial')),
            array('value'=>'lucida grande', 'label'=>Mage::helper('socialshare')->__('lucida grande')),
            array('value'=>'segoe ui', 'label'=>Mage::helper('socialshare')->__('segoe ui')),
            array('value'=>'tahoma', 'label'=>Mage::helper('socialshare')->__('tahoma')),
            array('value'=>'trebuchet ms', 'label'=>Mage::helper('socialshare')->__('trebuchet ms')),
            array('value'=>'verdana', 'label'=>Mage::helper('socialshare')->__('verdana'))
        );
    }
}