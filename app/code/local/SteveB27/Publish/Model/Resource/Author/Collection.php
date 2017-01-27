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
 
class SteveB27_Publish_Model_Resource_Author_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('publish/author');
    }
    
    protected function _toOptionArray($valueField='entity_id', $labelField='name', $additional=array())
    {
        $this->addAttributeToSelect('name');
        
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
    
    protected function _toOptionHash($valueField='entity_id', $labelField='name')
    {
        $this->addAttributeToSelect('name');
        
        return parent::_toOptionHash($valueField, $labelField);
    }
    
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        
        return $countSelect;
    }
    
    protected function _beforeLoad()
    {
	//	$this->groupByAttribute('entity_id');
		parent::_beforeLoad();
	}
}
