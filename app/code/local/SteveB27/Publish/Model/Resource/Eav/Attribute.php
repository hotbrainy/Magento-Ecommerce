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
 
class SteveB27_Publish_Model_Resource_Eav_Attribute extends Mage_Eav_Model_Entity_Attribute {
    const MODULE_NAME   = 'SteveB27_Publish';
    const ENTITY        = 'publish_eav_attribute';
    protected $_eventPrefix = 'publish_entity_attribute';
    protected $_eventObject = 'attribute';
    static protected $_labels = null;
    protected function _construct() {
        $this->_init('publish/attribute');
    }
    public function isScopeStore() {
        return $this->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE;
    }
    public function isScopeWebsite() {
        return $this->getIsGlobal() == Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_WEBSITE;
    }
    public function isScopeGlobal() {
        return (!$this->isScopeStore() && !$this->isScopeWebsite());
    }
    public function getBackendTypeByInput($type) {
        switch ($type){
            case 'file':
                //intentional fallthrough
            case 'image':
                return 'varchar';
                break;
            default:
                return parent::getBackendTypeByInput($type);
            break;
        }
    }
    protected function _beforeDelete(){
        if (!$this->getIsUserDefined()){
            throw new Mage_Core_Exception(Mage::helper('publish')->__('This attribute is not deletable'));
        }
        return parent::_beforeDelete();
    }
}