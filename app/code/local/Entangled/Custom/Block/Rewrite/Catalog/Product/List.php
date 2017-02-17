<?php

class Entangled_Custom_Block_Rewrite_Catalog_Product_List extends Mage_Catalog_Block_Product_List {

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->_getProductCollection();

        // use sortable parameters
        if ($orders = $this->getAvailableOrders()) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $toolbar->getCurrentOrder() ? $toolbar->getCurrentOrder() : $this->getSortBy();
        if ($sort) {
            $category = Mage::registry("current_category");
            if($category && $category->getName() == "Best Sellers"){
                $toolbar->setDefaultOrder("bestseller_rank");
                $toolbar->setDefaultDirection("ASC");
                $toolbar->setData('_current_grid_direction', "ASC");
            }elseif($sort == "release_date"){
                $toolbar->setDefaultDirection("DESC");
                $toolbar->setData('_current_grid_direction', "DESC");
            }else{
                $toolbar->setDefaultOrder($sort);
            }
        }
        if ($dir = $this->getDefaultDirection()) {
            $toolbar->setDefaultDirection($dir);
        }
        if ($modes = $this->getModes()) {
            $toolbar->setModes($modes);
        }

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent('catalog_block_product_list_collection', array(
            'collection' => $this->_getProductCollection()
        ));

        $this->_getProductCollection()->load();

        return call_user_func(array(get_parent_class(get_parent_class($this)), '_beforeToHtml'));
    }

    public function applySortWithoutRendering(){
        return $this::_beforeToHtml();
    }

}