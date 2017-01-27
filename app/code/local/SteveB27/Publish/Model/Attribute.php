<?php 
/**
 * Custom Publisher Models
 * 
 * Add custom model types, such as author, which can be used as a product
 * attribute while proviting additional details.
 * 
 * @license 	http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 * @copyright	Steven Brown March 12, 2016
 * @author		Steven Brown <steveb.27@outlook.com>
 */
 
class SteveB27_Publish_Model_Attribute extends Mage_Eav_Model_Entity_Attribute
{
    const SCOPE_STORE                           = 0;
    
    const SCOPE_GLOBAL                          = 1;
    
    const SCOPE_WEBSITE                         = 2;
    
    const MODULE_NAME                           = 'SteveB27_Publish';
    
    const ENTITY                                = 'eav_attribute';
    
    protected $_eventPrefix                     = 'publish_eav_attribute';
    
    protected $_eventObject                     = 'attribute';
    
    static protected $_labels                   = null;
    
    protected function _construct(){
        $this->_init('publish/attribute');
    }
    
    protected function _beforeSave(){
        $this->setData('modulePrefix', self::MODULE_NAME);
        if (isset($this->_origData['is_global'])) {
            if (!isset($this->_data['is_global'])) {
                $this->_data['is_global'] = self::SCOPE_GLOBAL;
            }
        }
        if ($this->getFrontendInput() == 'textarea') {
            if ($this->getIsWysiwygEnabled()) {
                $this->setIsHtmlAllowedOnFront(1);
            }
        }
        return parent::_beforeSave();
    }
    
    protected function _afterSave(){
        Mage::getSingleton('eav/config')->clear();
        return parent::_afterSave();
    }
    
    public function getIsGlobal(){
        return $this->_getData('is_global');
    }
    
    public function isScopeGlobal(){
        return $this->getIsGlobal() == self::SCOPE_GLOBAL;
    }
    
    public function isScopeWebsite(){
        return $this->getIsGlobal() == self::SCOPE_WEBSITE;
    }
    
    public function isScopeStore(){
        return !$this->isScopeGlobal() && !$this->isScopeWebsite();
    }
    
    public function getStoreId(){
        $dataObject = $this->getDataObject();
        if ($dataObject) {
            return $dataObject->getStoreId();
        }
        return $this->getData('store_id');
    }
    
    public function getSourceModel(){
        $model = $this->getData('source_model');
        if (empty($model)) {
            if ($this->getBackendType() == 'int' && $this->getFrontendInput() == 'select') {
                return $this->_getDefaultSourceModel();
            }
        }
        return $model;
    }
    
    public function getFrontendLabel(){
        return $this->_getData('frontend_label');
    }
    
    protected function _getLabelForStore(){
        return $this->getFrontendLabel();
    }
    
    public static function initLabels($storeId = null){
        if (is_null(self::$_labels)) {
            if (is_null($storeId)) {
                $storeId = Mage::app()->getStore()->getId();
            }
            $attributeLabels = array();
            $attributes = Mage::getResourceSingleton('catalog/product')->getAttributesByCode();
            foreach ($attributes as $attribute) {
                if (strlen($attribute->getData('frontend_label')) > 0) {
                    $attributeLabels[] = $attribute->getData('frontend_label');
                }
            }
            self::$_labels = Mage::app()->getTranslator()->getResource()->getTranslationArrayByStrings($attributeLabels, $storeId);
        }
    }
    
    public function _getDefaultSourceModel(){
        return 'eav/entity_attribute_source_table';
    }
}
