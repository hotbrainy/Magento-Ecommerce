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
 
class SteveB27_Publish_Model_Resource_Attribute extends Mage_Eav_Model_Resource_Entity_Attribute
{
    protected  function _afterSave(Mage_Core_Model_Abstract $object){
        $setup       = Mage::getModel('eav/entity_setup', 'publish_write');
        $entityType  = $object->getEntityTypeId();
        $setId       = $setup->getDefaultAttributeSetId($entityType);
        $groupId     = $setup->getDefaultAttributeGroupId($entityType);
        $attributeId = $object->getId();
        $sortOrder   = $object->getPosition();

        $setup->addAttributeToGroup($entityType, $setId, $groupId, $attributeId, $sortOrder);
        
        return parent::_afterSave($object);
    }
}