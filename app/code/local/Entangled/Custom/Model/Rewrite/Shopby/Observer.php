<?php

class Entangled_Custom_Model_Rewrite_Shopby_Observer extends Amasty_Shopby_Model_Observer {



    public function handleLayoutRender()
    {
        if (Mage::app()->getRequest()->getParam('is_scroll', false))
            return;

        /** @var Mage_Core_Model_Layout $layout */
        $layout = Mage::getSingleton('core/layout');
        $headBlock = $layout->getBlock('head');
        if (!$layout)
            return;

        $isAJAX = Mage::app()->getRequest()->getParam('is_ajax', false);
        $isAJAX = $isAJAX && Mage::app()->getRequest()->isXmlHttpRequest();
        if (!$isAJAX)
            return;

        $layout->removeOutputBlock('root');

        $page = $layout->getBlock('category.products');
        if (!$page){
            $page = $layout->getBlock('search.result');
        }

        if (!$page)
            return;

        $container = $layout->createBlock('core/template', 'amshopby_container');
        $container->setData('page', utf8_encode($this->_removeAjaxParam($page->toHtml())));
        $container->setData('title', $headBlock ? $headBlock->getTitle() : null);
        $container->setData('banner', $this->_getBannerBlock());

        $blocks = array();
        foreach ($layout->getAllBlocks() as $b){
            if (!in_array($b->getNameInLayout(), array('amshopby.navleft','amshopby.navleft2','amshopby.navtop','amshopby.navright', 'amshopby.top', 'amshopby.bottom', 'amfinder89'))){
                continue;
            }
            $b->setIsAjax(true);
            $html = $b->toHtml();
            if (!$html && false !== strpos($b->getBlockId(), 'amshopby-filters-'))
            {
                // compatibility with "shopper" theme
                // @see catalog/layer/view.phtml
                $queldorei_blocks = Mage::registry('queldorei_blocks');
                if ($queldorei_blocks AND !empty($queldorei_blocks['block_layered_nav']))
                {
                    $html = $queldorei_blocks['block_layered_nav'];
                }
            }
            if ($b->getBlockId()) {
                $blocks[$b->getBlockId()] = $this->_removeAjaxParam($html);
            }
        }

        if (!$blocks)
            return;

        $container->setData('blocks', $blocks);

        $layout->addOutputBlock('amshopby_container', 'toJson');
    }

    protected function _getBannerBlock(){
        return Mage::getSingleton('core/layout')->createBlock('bannerslider/default')->setTemplate('bannerslider/bannerslider.phtml')->setBannersliderId(3)->toHtml();
    }

}