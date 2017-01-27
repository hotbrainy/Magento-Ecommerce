<?php

class Entangled_Custom_Block_Rewrite_Downloadable_Sales_Order_Item_Renderer_Downloadable extends Mage_Downloadable_Block_Sales_Order_Item_Renderer_Downloadable {

    public function getDownloadUrl($item)
    {
        return $this->getUrl('downloadable/download/link', array('id' => $item->getLinkHash(), '_secure' => true));
    }

    public function getPdfLink($items)
    {
        foreach($items as $item){
            if("pdf" == explode(".", $item->getLinkFile())[1])
                return $this->getDownloadUrl($item);
        }
    }

    /**
     * Return true if target of link new window
     *
     * @return bool
     */
    public function getIsOpenInNewWindow()
    {
        return Mage::getStoreConfigFlag(Mage_Downloadable_Model_Link::XML_PATH_TARGET_NEW_WINDOW);
    }

}