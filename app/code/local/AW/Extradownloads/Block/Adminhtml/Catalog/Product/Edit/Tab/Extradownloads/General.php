<?php
/**
 * Extra Downloads tab content General
 */
class AW_Extradownloads_Block_Adminhtml_Catalog_Product_Edit_Tab_Extradownloads_General
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Template path
     */
    const TAB_GENERAL_TEMPLATE = "extradownloads/product/edit/tab/general.phtml";


    /**
     * This is constructor
     * Set General template
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate(self::TAB_GENERAL_TEMPLATE);
    }

    /**
     * Get model of the product that is being edited
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Retrieve Add Button HTML
     * @return String
     */
    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('extradownloads')->__('Add New Row'),
                'id' => 'add_extradownloads_item',
                'class' => 'add',
            ));
        return $addButton->toHtml();
    }

    /**
     * Retrieve extradownloads array
     * @return array
     */
    public function getExtradownloadsData()
    {
        $extradownloadsArr = array();

        if ($product = $this->getProduct()){
            $store_id = $product->getStoreId() ? $product->getStoreId() : 0;

            $extradownloads = Mage::getModel('extradownloads/file')
                ->getCollection()
                ->addAttributeToFilter('product_id', $product->getId())
                ->setStore($store_id)
                ->addAttributeToSelect(array(
                        'title',
                        'visible',
                        'file',
                        'url',
                        'type',
                        'sort_order',
                    ));

            # prepare data to show in accordion
            foreach ($extradownloads as $item) {
                
                if ($item->getType() == AW_Extradownloads_Helper_File::EXTRA_TYPE_FILE){
                    $item->setUrl('');
                } elseif ($item->getType() == AW_Extradownloads_Helper_File::EXTRA_TYPE_URL) {
                    $item->setFile('');
                }
                
                $tmpExtradownloadsItem = array(
                    'extradownloads_id' => $item->getId(),
                    'title' => $item->getTitle(),
                    'visible' => $item->getVisible(),
                    'url' => $item->getUrl(),
                    'sort_order' => $item->getSortOrder(),
                    'type' => $item->getType(),
                                                            
                    # Default params
                    'use_default_title' => $item->getUseDefaultTitle(),
                    'use_default_visible' => $item->getUseDefaultVisible(),
                    'use_default_type' => $item->getUseDefaultType(),
                    'use_default_sort_order' => $item->getUseDefaultSortOrder(),
                );

                $file = Mage::helper('extradownloads/file')->getFilePath(
                    AW_Extradownloads_Model_File::getBasePath(),
                    $item->getFile()
                );
                if ($item->getFile() && is_file($file)) {
                    $tmpExtradownloadsItem['file_save'] = array(
                        array(
                            'file' => $item->getFile(),
                            'name' => Mage::helper('extradownloads/file')->getFileFromPathFile($item->getFile()),
                            'size' => filesize($file),
                            'status' => 'old'
                        ));
                }
                if ($this->getProduct() && $item->getStoreTitle()) {
                    $tmpExtradownloadsItem['store_title'] = $item->getStoreTitle();
                }
                $extradownloadsArr[] = new Varien_Object($tmpExtradownloadsItem);
            }
            return $extradownloadsArr;
       }
    }
    /**
     * Check exists defined extradownloads title
     * @return boolean
     */
    public function getUsedDefault()
    {
        return is_null($this->getProduct()->getAttributeDefaultValue('extradownloads_title'));
    }

    /**
     * Prepare layout
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'upload_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                    'id'      => '',
                    'label'   => Mage::helper('extradownloads')->__('Upload Files'),
                    'type'    => 'button',
                    'onclick' => 'Extradownloads.massUploadByType(\'extradownloads\')'
                ))
        );
    }

    /**
     * Retrieve Upload button HTML
     * @return String
     */
    public function getUploadButtonHtml()
    {
        return $this->getChild('upload_button')->toHtml();
    }

    /**
     * Retrive config json
     * @return String
     */
    public function getConfigJson()
    {
        $this->getConfig()->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('adminhtml/awextradownloads_file/upload', array('_secure' => true)));
        $this->getConfig()->setParams(array('form_key' => $this->getFormKey()));
        $this->getConfig()->setFileField('extradownloads');
        $this->getConfig()->setFilters(array(
            'all'    => array(
                'label' => Mage::helper('adminhtml')->__('All Files'),
                'files' => array('*.*')
            )
        ));
        $this->getConfig()->setReplaceBrowseWithRemove(true);
        $this->getConfig()->setWidth('32');
        $this->getConfig()->setHideUploadButton(false);
        return Zend_Json::encode($this->getConfig()->getData());
    }

    /**
     * Retrive config object
     * @return Varien_Config
     */
    public function getConfig()
    {
        if(is_null($this->_config)) {
            $this->_config = new Varien_Object();
        }
        return $this->_config;
    }
}





