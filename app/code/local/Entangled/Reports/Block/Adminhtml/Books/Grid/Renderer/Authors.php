<?php

class Entangled_Reports_Block_Adminhtml_Books_Grid_Renderer_Authors
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row)
    {
        $product = Mage::getModel('catalog/product')
            ->setStoreId(0)
            ->setData("publish_author",$row->getData("author"));

        return implode(", ", Mage::helper('publish/author')->getAuthorsList($product,false));
    }
}