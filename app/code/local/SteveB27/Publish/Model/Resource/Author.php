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
 
class SteveB27_Publish_Model_Resource_Author extends Mage_Eav_Model_Entity_Abstract
{
	public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType(SteveB27_Publish_Model_Author::ENTITY);
        $this->setConnection(
            $resource->getConnection('publish_read'),
            $resource->getConnection('publish_write')
        );
    }
    
    public function getMainTable() {
        return $this->getEntityTable();
    }
    
    public function checkUrlKey($urlKey, $storeId, $active = true){
        $stores = array(Mage_Core_Model_App::ADMIN_STORE_ID, $storeId);
        $select = $this->_initCheckUrlKeySelect($urlKey, $stores);
        if (!$select){
            return false;
        }
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('e.entity_id')
            ->limit(1);
            
        return $this->_getReadAdapter()->fetchOne($select);
    }
    
    protected function _initCheckUrlKeySelect($urlKey, $store){
        $urlRewrite = Mage::getModel('eav/config')->getAttribute('author', 'url_key');
        if (!$urlRewrite || !$urlRewrite->getId()){
            return false;
        }
        $table = $urlRewrite->getBackend()->getTable();
        $select = $this->_getReadAdapter()->select()
            ->from(array('e' => $table))
            ->where('e.attribute_id = ?', $urlRewrite->getId())
            ->where('e.value = ?', $urlKey)
            ->where('e.store_id IN (?)', $store)
            ->order('e.store_id DESC');
            
        return $select;
    }
    
    public function getAuthorByEmail($email) {
		$attribute = Mage::getModel('eav/config')->getAttribute('author', 'email');
        if (!$attribute || !$attribute->getId()){
            return false;
        }
        $table = $attribute->getBackend()->getTable();
        $select = $this->_getReadAdapter()->select()
            ->from(array('e' => $table))
            ->where('e.attribute_id = ?', $attribute->getId())
            ->where('e.value = ?', $email)
            ->order('e.store_id DESC');
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns('e.entity_id')
            ->limit(1);
            
        return $this->_getReadAdapter()->fetchOne($select);
	}
}
