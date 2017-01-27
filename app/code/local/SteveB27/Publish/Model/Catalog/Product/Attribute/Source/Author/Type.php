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
 
class SteveB27_Publish_Model_Catalog_Product_Attribute_Source_Author_Type extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $collection = Mage::getModel('publish/author')->getCollection();
			$collection->addAttributeToSelect('name');
			$collection->setOrder('name', 'asc');  // TODO: Fix iterator bug
            $this->_options = array();
            foreach($collection as $author) {
                $this->_options[] = array(
                    'label' => Mage::helper('publish')->__($author->getName()),
                    'value' => $author->getId(),
                );
            }
        }
        return $this->_options;
    }

    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
    
    public function getOptionArray()
    {
        $optionArray = array();
        $options = $this->toOptionArray();
        foreach($options as $option) {
            $optionArray[$option['value']] = $option['label'];
        }
        
        return $optionArray;
    }
    
    public function addValueSortToCollection($collection, $dir = 'asc')
    {
        $adminStore  = Mage_Core_Model_App::ADMIN_STORE_ID;
        $valueTable1 = $this->getAttribute()->getAttributeCode() . '_t1';
        $valueTable2 = $this->getAttribute()->getAttributeCode() . '_t2';
 
        $collection->getSelect()->joinLeft(
            array($valueTable1 => $this->getAttribute()->getBackend()->getTable()),
            "`e`.`entity_id`=`{$valueTable1}`.`entity_id`"
            . " AND `{$valueTable1}`.`attribute_id`='{$this->getAttribute()->getId()}'"
            . " AND `{$valueTable1}`.`store_id`='{$adminStore}'",
            array()
        );
 
        if ($collection->getStoreId() != $adminStore) {
            $collection->getSelect()->joinLeft(
                array($valueTable2 => $this->getAttribute()->getBackend()->getTable()),
                "`e`.`entity_id`=`{$valueTable2}`.`entity_id`"
                . " AND `{$valueTable2}`.`attribute_id`='{$this->getAttribute()->getId()}'"
                . " AND `{$valueTable2}`.`store_id`='{$collection->getStoreId()}'",
                array()
            );
            $valueExpr = new Zend_Db_Expr("IF(`{$valueTable2}`.`value_id`>0, `{$valueTable2}`.`value`, `{$valueTable1}`.`value`)");
 
        } else {
            $valueExpr = new Zend_Db_Expr("`{$valueTable1}`.`value`");
        }
 
 
 
        $collection->getSelect()
            ->order($valueExpr, $dir);
 
        return $this;
    }
 
    public function getFlatColums()
    {
        $columns = array(
            $this->getAttribute()->getAttributeCode() => array(
                'type'      => 'varchar',
                'unsigned'  => false,
                'is_null'   => true,
                'default'   => null,
                'extra'     => null
            )
        );
        return $columns;
    }
 
 
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel('eav/entity_attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
