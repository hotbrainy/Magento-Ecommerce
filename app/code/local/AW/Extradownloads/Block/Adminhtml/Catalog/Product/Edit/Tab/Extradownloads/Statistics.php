<?php

/**
 * Extra Downloads tab content Statistics
 */
class AW_Extradownloads_Block_Adminhtml_Catalog_Product_Edit_Tab_Extradownloads_Statistics
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Template path
     */
    const TAB_STATISTICS_TEMPLATE = "extradownloads/product/edit/tab/statistics.phtml";

    /**
     * Cache of Statistics collection
     * @var AW_Extradownloads_Model_Entity_File_Collection
     */
    private $_collection = null;

    /**
     * This is constructor
     * Set Statistics template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate(self::TAB_STATISTICS_TEMPLATE);

        if (!$this->getStoreId() && $product=$this->getProduct()){
            $this->setStoreId($product->getStoreId());
        }
    }

    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Retrieve Reset Button HTML
     * @return String
     */
    public function getResetButtonHtml()
    {
        $product = $this->getProduct();
        $store_id = $product ? $product->getStoreId() : ($this->getStoreId() ? $this->getStoreId() : 0);
        $product_id = $product ? $product->getId() : $this->getProductId();        
        $url = Mage::getModel('adminhtml/url')->getUrl('adminhtml/awextradownloads_file/reset', array(
                                                'product_id'=>$product_id,
                                                'store_id'=>$store_id,
                                                '_secure' => true
                                            ));
        $resetButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('extradownloads')->__('Reset'),
                'id' => 'reset_extradownloads_statistics',
                'class' => 'delete',
                'onclick' =>"resetEDStatistics('$url'); return false;"
            ));
        return $resetButton->toHtml();
    }

    /**
     * Returns Statistics collection
     * @return AW_Extradownloads_Model_Entity_File_Collection
     */
    public function getCollection()
    {
        if (!$this->_collection){
            $product = $this->getProduct();
            $store_id = $product ? $product->getStoreId() : ($this->getStoreId() ? $this->getStoreId() : 0);
            $product_id = $product ? $product->getId() : $this->getProductId();
            $this->_collection = Mage::getModel('extradownloads/file')
                    ->getCollection()
                    ->setStore($store_id)
                    ->addAttributeToSelect(array('title', 'downloads'))
                    ->addAttributeToFilter('product_id', $product_id)
                    ->addAttributeToSort('created_at','desc')
                    ;
        }
        return $this->_collection;
    }
}