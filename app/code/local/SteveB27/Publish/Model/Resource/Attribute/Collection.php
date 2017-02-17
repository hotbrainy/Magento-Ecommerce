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
 
class SteveB27_Publish_Model_Resource_Attribute_Collection extends Mage_Eav_Model_Resource_Entity_Attribute_Collection
{
    protected function _initSelect() {
            $this->getSelect()->from(array('main_table' => $this->getResource()->getMainTable()))
                ->where('main_table.entity_type_id=?', Mage::getModel('eav/entity')->setType(SteveB27_Publish_Model_Author::ENTITY)->getTypeId())
                ->join(
                    array('additional_table' => $this->getTable('publish/publish_eav_attribute')),
                    'additional_table.attribute_id=main_table.attribute_id'
                );
        return $this;
    }
    
    public function setEntityTypeFilter($typeId) {
        return $this;
    }
    
    public function addVisibleFilter() {
        return $this->addFieldToFilter('additional_table.is_visible', 1);
    }
    
    public function addEditableFilter() {
        return $this->addFieldToFilter('additional_table.is_editable', 1);
    }
}