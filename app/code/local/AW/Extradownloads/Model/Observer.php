<?php
    /**
     * aheadWorks Co.
     *
     * NOTICE OF LICENSE
     *
     * This source file is subject to the EULA
     * that is bundled with this package in the file LICENSE.txt.
     * It is also available through the world-wide-web at this URL:
     * http://ecommerce.aheadworks.com/LICENSE-M1.txt
     *
     * @category   AW
     * @package    AW_Extradownloads
     * @copyright  Copyright (c) 2010 aheadWorks Co. (http://www.aheadworks.com)
     * @license    http://ecommerce.aheadworks.com/LICENSE-M1.txt
     */

/**
 * Extra Downloads event observer
 */
class AW_Extradownloads_Model_Observer
{
    /**
     * Prepare product to save
     * @param   Varien_Object $observer
     * @return  AW_Extradownloads_Model_Observer
     */
    public function prepareProductSave($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();

        if ($extradownloads = $request->getPost('extradownloads')) {            
            $this->_saveProductExtradownloads($product, $extradownloads);
        }        
        return $this;
    }

    /**
     * Save Extra Downloads files of product (just if they exists and need it!)
     * @param Mage_Catalog_Model_Product $product Product that need to save
     *                                  Extra Downloads Files
     * @param array $extradownloads Extra Downloads File array
     */
    protected function _saveProductExtradownloads($product, $extradownloads)
    {
        $extradownloads = $extradownloads['extradownloads'];
        if ($extradownloads && is_array($extradownloads) && count($extradownloads)){
            foreach ($extradownloads as $item){
                $extraObj = new Varien_Object($item);
                try{
                    $extraItem = Mage::getModel('extradownloads/file')->setStoreId($product->getStoreId());

                    # if it's saved earler, will update this data
                    if ($id = $extraObj->getExtradownloadsId()){
                        $extraItem->load($id);

                        # Clear DB if item deleted
                        if ($extraObj->getIsDelete()){
                            $extraItem->delete();
                        }
                    }
                    if ($extraObj->getIsDelete()){
                        continue;
                    }
                    $extraItem->setProductId($product->getId());

                    foreach ($extraObj->getData() as $key => $value){
                        if (strpos($key, 'use_default_') === false){
                            # Save  value
                            $extraItem->setData($key, $value);
                            if ($product->getStoreId()){
                                # set flag for insert store data
                                $extraItem->setData('need_store_update_'.$key, 1);
                            }
                        }                       
                    }

                    # Set up downloads not NULLs
                    $extraItem->setDownloads(0);
                    
                    # unserialize file name
                    $files = array();
                    if (isset($item['file'])) {
                        $files = Zend_Json::decode($item['file']);
                        unset($item['file']);
                    }

                    # save file if need
                    if ($extraObj->getType() == AW_Extradownloads_Helper_File::EXTRA_TYPE_FILE) {
                        $extraName = Mage::helper('extradownloads/file')->moveFileFromTmp(
                            AW_Extradownloads_Model_File::getBaseTmpPath(),
                            AW_Extradownloads_Model_File::getBasePath(),
                            $files
                        );
                        $extraItem->setFile($extraName);
                        $extraItem->setUrl($product->getStoreId() ? '' : null);
                    } elseif( $extraObj->getType() == AW_Extradownloads_Helper_File::EXTRA_TYPE_URL ) {
                        $extraItem->setFile($product->getStoreId() ? '' : null);
                    }

                    # save item
                    $extraItem->save();
                                          
                    foreach ($extraObj->getData() as $key => $value){
                        if (strpos($key, 'use_default_') !== false){
                            # Delete if exists
                            $extraItem->resetToDefault($extraItem, str_replace('use_default_', '', $key), $product->getStoreId());
                        }
                    }
                } catch (Exception $e) {
                    Mage::throwException($e->getMessage());
                }
            }          
        }
    }
}