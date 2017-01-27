<?php

/**
 * Extra Downloads File Model
 */
class AW_Extradownloads_Model_File extends Mage_Core_Model_Abstract
{
    /**
     * File Helper Instance
     * @var AW_Extradownloads_Helper_File
     */
    protected $_fileHelper = null;

    /**
     * Class constructor
     */
    protected function _construct()
    {
        $this->_init('extradownloads/file');
    }

    /**
     * Retrives Base Tmp Path for save files when uploading
     * @return String
     */
    public static function getBaseTmpPath()
    {
        return Mage::getBaseDir('media') . DS . 'extradownloads' . DS . 'tmp';
    }

    /**
     * Retrieve Base files path
     * @return string
     */
    public static function getBasePath()
    {
        return Mage::getBaseDir('media') . DS . 'extradownloads' . DS . 'files';
    }

    /**
     * Retrives flag use default title value
     * @return Boolean
     */
    public function getUseDefaultTitle()
    {
        return Mage::getResourceModel('extradownloads/file')->getUseDefaultTitle($this->getEntityId());
    }

    /**
     * Retrives flag use default visible value
     * @return Boolean
     */
    public function getUseDefaultVisible()
    {
        return Mage::getResourceModel('extradownloads/file')->getUseDefaultVisible($this->getEntityId());
    }

    /**
     * Retrives flag use default file values
     * @return Boolean
     */
    public function getUseDefaultType()
    {
        return Mage::getResourceModel('extradownloads/file')->getUseDefaultType($this->getEntityId());
    }

    /**
     * Retrives flag use default sort order value
     * @return Boolean
     */
    public function getUseDefaultSortOrder()
    {
        return Mage::getResourceModel('extradownloads/file')->getUseDefaultSortOrder($this->getEntityId());
    }
    
    /**
     * Reset value to default if flag is set
     *
     * @param   Mage_Core_Model_Abstract $object
     * @param   String $attribute_code
     * @param   int $storeId
     * @return  Mage_Core_Model_Abstract
     */
    public function resetToDefault($object, $attribute_code, $storeId)
    {
        $this->getResource()->resetToDefault($object, $attribute_code, $storeId);
        return $this;
    }

    /**
     * Retrives downloads summary for all stores
     * @return Integer
     */
    public function getDownloadsSummary()
    {
        $summ = 0;

        $attribute = $this->getResource()->getAttribute('downloads');
        $attribute_id = $attribute->getId();
        
        $entity_id = $this->getEntityId();
        $connection = $this->getResource()->getReadConnection();
        $table = $attribute->getBackendTable();

        $select = new Zend_Db_Select($connection);
        $select
            ->from($table, array('summ'=>'SUM(value)'))
            ->where('attribute_id = ?', $attribute_id)
            ->where('entity_id = ?', $entity_id)
            ->where('store_id <> ?', $this->getResource()->getDefaultStoreId())
            ;
        try {
            $summ = $select->getAdapter()->fetchOne($select->__toString());
            $summ = ($summ === null) ? 0 : $summ;
        } catch(Exception $e) {
            $summ = 0;
            Mage::throwException($e->getMessage());
        }
        return $summ;
    }

    /**
     * Reset all statistics for Product [for Same Store]
     *
     * @param int|null $store_id
     * @return AW_Extradownloads_Model_File
     */
    public function resetProductStatistics($store_id = null)
    {
        $attribute = $this->getResource()->getAttribute('downloads');
        $attribute_id = $attribute->getId();
        
        $entityId = $this->getEntityId();
        $connection = $this->getResource()->getWriteConnection();

        try{
            $connection->update($attribute->getBackend()->getTable(),
                    array('value' => 0),
                    'attribute_id='.(int)$attribute_id.' AND '.
                    'entity_id='.(int)$entityId.' AND '.
                    ($store_id ? 'store_id='.(int)$store_id : 'store_id<>'.(int)$this->getResource()->getDefaultStoreId())
                );
        } catch(Exception $e) {
            Mage::throwException($e->getMessage());
        }
        return $this;
    }

    /**
     * Reset all statistics for All Products and Stores
     * Be Carefull!!!! It's very dangerous )))
     * 
     * @return AW_Extradownloads_Model_File
     */
    public function resetAllStatistics()
    {
        $attribute = $this->getResource()->getAttribute('downloads');
        $attribute_id = $attribute->getId();

        $connection = $this->getResource()->getWriteConnection();

        try{
            $connection->update($attribute->getBackend()->getTable(),
                    array('value' => 0),
                    'attribute_id='.(int)$attribute_id
                );
        } catch(Exception $e) {
            Mage::throwException($e->getMessage());
        }
        return $this;
    }


    /**
     * Retrives File Helper Instance
     * @return AW_Extradownloads_Helper_File
     */
    protected function _getFileHelper()
    {
        if (!$this->_fileHelper){
            $this->_fileHelper = Mage::helper('extradownloads/file');
        }
        return $this->_fileHelper;
    }

    /**
     * Retrives BaseFileName of file
     * @return string
     */
    public function getFileName()
    {
        return pathinfo($this->getFilePath(), PATHINFO_BASENAME);;
    }

    /**
     * Retrives full file path
     * @return string
     */
    public function getFilePath()
    {
        return $this->getBasePath().$this->getFile();
    }

    /**
     * Retrives type of file content
     * @return string
     */
    public function getFileType()
    {
        if (function_exists('mime_content_type')) {
            return mime_content_type( $this->getFilePath() );
        } else {
            return $this->_getFileHelper()->getFileType( $this->getFileName() );
        }
    }

    /**
     * Retrive file content
     * @return mixed
     */
    public function getFileContent()
    {
        return @file_get_contents($this->getFilePath());
    }

    /**
     * Retrives length of file content
     * @return int
     */
    public function getFileContentLength()
    {
        return @filesize($this->getFilePath());
    }
}