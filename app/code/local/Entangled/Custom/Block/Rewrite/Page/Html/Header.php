<?php

class Entangled_Custom_Block_Rewrite_Page_Html_Header extends Mage_Page_Block_Html_Header {

    /**
     * Check if current url is url for home page
     *
     * @return true
     */
    public function getIsHomePage()
    {
        return $this->getUrl(array('_current'=>true, '_use_rewrite'=>true)) == $this->getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true));
    }

}