<?php
class SteveB27_Publish_Block_Author_View_Products extends Mage_Catalog_Block_Product_List
{
	public function getLoadedProductCollection()
    {
		$author = Mage::registry('current_author');
        $toolbar = $this->getToolbarBlock();
        $sort = $toolbar->getCurrentOrder() ? $toolbar->getCurrentOrder() : $this->getSortBy();
        $dir = $this->getDefaultDirection() ? $this->getDefaultDirection() : "ASC";
        /** @var Entangled_Custom_Model_Rewrite_Resource_Catalog_Product_Collection $collection */
		$collection = $author->getProducts($sort,$dir);

        $this->_productCollection = $collection;
		
		return $collection;
	}

    public function getToolbarHtml(){
        return $this->getToolbarBlock()
            ->removeOrderFromAvailableOrders('position')
            ->removeOrderFromAvailableOrders('publish_author')
            ->setTemplate('catalog/product/list/toolbar-author.phtml')
            ->toHtml();
    }

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
            $toolbar->setDefaultOrder($sort);
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

        return $this;
    }
}