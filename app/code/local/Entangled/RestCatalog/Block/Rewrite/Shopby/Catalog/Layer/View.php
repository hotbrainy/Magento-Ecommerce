<?php
class Entangled_RestCatalog_Block_Rewrite_Shopby_Catalog_Layer_View extends Amasty_Shopby_Block_Catalog_Layer_View {

    protected function _afterToHtml($html)
    {
        $html = call_user_func(array(get_parent_class(get_parent_class($this)), '_afterToHtml'),$html);

        $queldorei = false;
        if (!$html){
            // compatibility with "shopper" theme
            // @see catalog/layer/view.phtml
            $queldorei_blocks = Mage::registry('queldorei_blocks');
            if ($queldorei_blocks AND !empty($queldorei_blocks['block_layered_nav'])) {
                $html = $queldorei_blocks['block_layered_nav'];
            }
            if (!$html){
                return '';
            }
            $queldorei = true;
        }

        $pos = strrpos($html, '</div>');
        if ($pos !== false) {
            //add an overlay before closing tag

            /******** ENTANGLED CUSTOM - Remove filters overlay
            $html = substr($html, 0, strrpos($html, '</div>'))
            . '<div style="display:none" class="amshopby-overlay"></div>'
            . '</div>';
             *********/
        }


        // to make js and css work for 1.3 also
        $html = str_replace('class="narrow-by', 'class="block-layered-nav narrow-by', $html);
        // add selector for ajax
        $html = str_replace('block-layered-nav', 'block-layered-nav ' . $this->getBlockId(), $html);

        if (Mage::getStoreConfig('amshopby/general/enable_collapsing')) {
            $html = str_replace('block-layered-nav', 'block-layered-nav amshopby-collapse-enabled', $html);
        }

        $enableOverflowScroll = Mage::getStoreConfig('amshopby/block/enable_overflow_scroll');
        if ($enableOverflowScroll) {
            $html = str_replace('block-layered-nav', 'block-layered-nav amshopby-overflow-scroll-enabled', $html);
            if (strpos($html, 'block-layered-nav')) {
                $html = $html
                    . '<style>'
                    . 'div.amshopby-overflow-scroll-enabled div.block-content dl dd > ol:first-of-type { max-height: ' . $enableOverflowScroll . 'px; overflow-y: auto; }'
                    . '</style>';
            }
        }

        // we don't want to move this into the template are different in custom themes
        foreach ($this->getFilters() as $f){
            $name = $this->__($f->getName());
            if ($f->getCollapsed() && !$f->getHasSelection()){
                $html = preg_replace('|(<dt[^>]*)(>'. preg_quote($name, '|') .')|iu', '$1 class="amshopby-collapsed"$2', $html);
            }
            $comment = $f->getComment();
            if ($comment){
                $img = Mage::getDesign()->getSkinUrl('images/amshopby-tooltip.png');
                $img = ' <img class="amshopby-tooltip-img" src="'.$img.'" width="9" height="9" alt="'.htmlspecialchars($comment).'" />';

                $pattern = '@(<dt[^>]*>\s*' . preg_quote($name, '@') . ')\s*(</dt>)@ui';
                $replacement = '$1 ' . $img . '$2';
                $html = preg_replace($pattern, $replacement, $html);
            }

        }

        if ($queldorei AND !empty($queldorei_blocks['block_layered_nav'])) {
            // compatibility with "shopper" theme
            // @see catalog/layer/view.phtml
            Mage::unregister('queldorei_blocks');
            $queldorei_blocks['block_layered_nav'] = $html;
            Mage::register('queldorei_blocks', $queldorei_blocks);
            return '';
        }

        $this->saveLayerCache();

        return $html;
    }

    protected function _prepareLayout()
    {
        if($productsBlock = Mage::app()->getLayout()->getBlock('category.products')) {
            $productsBlock->getCmsBlockHtml();
        }

        $pos = Mage::getStoreConfig('amshopby/block/categories_pos');
        if ($this->_notInBlock($pos)){
            $this->_categoryBlockName = 'amshopby/catalog_layer_filter_empty';
        }
        if (Mage::getStoreConfig('amshopby/general/stock_filter_pos') >= 0) {
            $stockBlock = $this->getLayout()->createBlock('amshopby/catalog_layer_filter_stock')
                ->setLayer($this->getLayer())
                ->init();

            $this->setChild('stock_filter', $stockBlock);
        }

        if (Mage::getStoreConfig('amshopby/general/rating_filter_pos') >= 0) {
            $ratingBlock = $this->getLayout()->createBlock('amshopby/catalog_layer_filter_rating')
                ->setLayer($this->getLayer())
                ->init();
            $this->setChild('rating_filter', $ratingBlock);
        }

        if (Mage::registry('amshopby_layout_prepared')){
            return parent::_prepareLayout();
        }
        else {
            Mage::register('amshopby_layout_prepared', true);
        }

        if (!Mage::getStoreConfigFlag('customer/startup/redirect_dashboard')) {
            $url = Mage::helper('amshopby/url')->getFullUrl($_GET);
            Mage::getSingleton('customer/session')
                ->setBeforeAuthUrl($url);
        }

        $head = $this->getLayout()->getBlock('head');
        if ($head){
            $head->addJs('amasty/amshopby/amshopby.js');

            if (Mage::getStoreConfig('amshopby/block/slider_use_ui')) {
                $head->addJs('amasty/amshopby/jquery.min.js');
                $head->addJs('amasty/amshopby/jquery-ui.min.js');
                $head->addJs('amasty/amshopby/jquery.ui.touch-punch.min.js');
                $head->addJs('amasty/amshopby/amshopby-jquery.js');
            }

            if (Mage::getStoreConfigFlag('amshopby/block/ajax')){
                $request = Mage::app()->getRequest();

                $isProductPage = $request->getControllerName() == "product" &&
                    $request->getActionName() == "view";

                if (!$isProductPage)
                    $head->addJs('amasty/amshopby/amshopby-ajax-entangled-rest.js','data-cfasync="false"');
            }
        }

        return parent::_prepareLayout();
    }

}