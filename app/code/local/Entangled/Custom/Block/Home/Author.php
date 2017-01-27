<?php

class Entangled_Custom_Block_Home_Author extends Mage_Core_Block_Template {

    protected $_template = "entangled/custom/home/author.phtml";

    protected function _construct(){
        parent::_construct();

        $this->setCacheLifetime(3600);
    }

    public function getSliderHtml(){
        $block = $this->getLayout()->createBlock('entangled_custom/home_author_list');

        $authorId = Mage::getStoreConfig("entangled_custom_author/settings/featured_author");
        $block->setData("author_id",$authorId);
        $block->setData("breakpoints","[0, 1], [480, 2], [640, 3], [960, 5]");
        $block->setData("img_width","152");
        $block->setData("centered","1");
        $block->setData("hide_button","1");

        return $block->toHtml();
    }

    public function getAuthorWidgetHtml(){
        $authorId = Mage::getStoreConfig("entangled_custom_author/settings/featured_author");
        $filter = Mage::getModel('widget/template_filter');
        echo $filter->filter('{{widget type="publish/widget" widget_title="Featured Author" author_id="'.$authorId.'" template="publish/author/widget.phtml"}}');
    }

}