<?php

/**
 * Download Section Block
 */
class AW_Extradownloads_Block_Product_Section extends Mage_Core_Block_Template
{
    /**
     * Default section template
     */
    const DEFAULT_SECTION_TEMPLATE = "extradownloads/product/section.phtml";

    /**
     * Product cache
     * @var Mage_Catalog_Model_Product
     */
    private $_product = null;

    /**
     * This is constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate(self::DEFAULT_SECTION_TEMPLATE);
    }

    /**
     * Returns title of section for current product
     * @return String
     */
    public function getSectionTitle()
    {
        $title = Mage::getStoreConfig('extradownloads/general/default_title');
        
        if ($this->getProduct() && $this->getProduct()->getExtradownloadsTitle()){
            $title = $this->getProduct()->getExtradownloadsTitle();
        }
        return $title;
    }

    /**
     * Enable showing of section only for page with CURRENT_PRODUCT
     * in registry
     * @return boolean
     */
    public function getShowBlock()
    {
        return (Mage::getStoreConfig('extradownloads/general/enabled')
                    && $this->getProduct()
                    && $this->getProduct()->getExtradownloadsEnabled()
                );
    }

    /**
     * Retrives current product
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (!$this->_product){
            $this->_product = Mage::registry('current_product');
        }
        return $this->_product;
    }

    /**
     * Retrives downloadlink
     * @param AW_Extradownloads_Model_File $file File to download
     * @return String
     */
    public function getDownloadLink($file = null)
    {
        if (!$file) {
            return;
        }
        $link = $this->getUrl('extradownloads/file/get', array('id' => $file->getId(),
                              '_secure'=>Mage::app()->getStore(true)->isCurrentlySecure()));        
        return $link;
    }

    /**
     * Retrives product files collection
     * @return AW_Extradownloads_Model_Entity_File_Collection
     */
    public function getFiles()
    {
        $collection = Mage::getModel('extradownloads/file')
                ->getCollection()
                ->setStore(Mage::app()->getStore()->getId())
                ->addAttributeToSelect(array(
                        'title',
                        'visible',
                        'file',
                        'url',
                        'type',
                        'sort_order',
                    ))
                ->addAttributeToFilter('product_id', $this->getProduct()->getId())
                ->addAttributeToFilter('visible', '1')
                ->setOrder('sort_order', 'asc')
        ;
        return $collection;
    }

}