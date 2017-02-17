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


/**
 * Report Sold Products collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class SteveB27_Publish_Model_Resource_Author_Report_Collection extends Mage_Reports_Model_Resource_Product_Collection
{
    const SELECT_COUNT_SQL_TYPE_CART           = 1;

    /**
     * Author entity identifier
     *
     * @var int
     */
    protected $_authorEntityId;

    /**
     * Author entity table name
     *
     * @var string
     */
    protected $_authorEntityTableName;

    /**
     * Author varchar table name
     *
     * @var string
     */
    protected $_authorVarcharTableName;

    /**
     * Author name attribute id
     * 
     * @var int
     */
    protected $_authorNameAttributeId;

    /**
     * Author entity type identifier
     *
     * @var int
     */
    protected $_authorEntityTypeId;

    /**
     * select count
     *
     * @var int
     */
    protected $_selectCountSqlType               = 0;

    /**
     * Init main class options
     *
     */
    public function __construct()
    {
        $author = Mage::getResourceSingleton('publish/author');
        /* @var $author SteveB27_Publish_Model_Entity_Author */
        $this->setAuthorEntityId($author->getEntityIdField());
        $this->setAuthorEntityTableName($author->getEntityTable());
        $this->setAuthorVarcharTableName(Mage::getModel('core/resource')->getTableName('publish_author_varchar'));
        $this->setAuthorEntityTypeId($author->getTypeId());
        
        $this->_authorNameAttributeId = Mage::getModel('eav/entity_attribute')->loadByCode($author->getTypeId(), 'name')->getId();

        parent::__construct();
    }

    /**
     * Set author entity id
     *
     * @param int $value
     * @return SteveB27_Publish_Model_Resource_Author_Collection
     */
    public function setAuthorEntityId($entityId)
    {
        $this->_authorEntityId = (int)$entityId;
        return $this;
    }

    /**
     * Get author entity id
     *
     * @return int
     */
    public function getAuthorEntityId()
    {
        return $this->_authorEntityId;
    }

    /**
     * Set author entity table name
     *
     * @param string $value
     * @return SteveB27_Publish_Model_Resource_Author_Report_Collection
     */
    public function setAuthorEntityTableName($value)
    {
        $this->_authorEntityTableName = $value;
        return $this;
    }

    /**
     * Get author entity table name
     *
     * @return string
     */
    public function getAuthorEntityTableName()
    {
        return $this->_authorEntityTableName;
	}

    /**
     * Set author entity table name
     *
     * @param string $value
     * @return SteveB27_Publish_Model_Resource_Author_Report_Collection
     */
    public function setAuthorVarcharTableName($value)
    {
        $this->_authorVarcharTableName = $value;
        return $this;
    }

    /**
     * Get author entity table name
     *
     * @return string
     */
    public function getAuthorVarcharTableName()
    {
        return $this->_authorVarcharTableName;
    }

    /**
     * Set author entity type id
     *
     * @param int $value
     * @return SteveB27_Publish_Model_Resource_Author_Report_Collection
     */
    public function setAuthorEntityTypeId($value)
    {
        $this->_productEntityTypeId = $value;
        return $this;
    }

    /**
     * Get author entity tyoe id
     *
     * @return int
     */
    public function getAuthorEntityTypeId()
    {
        return  $this->_authorEntityTypeId;
    }

    /**
     * Join fields
     *
     * @return SteveB27_Publish_Model_Resource_Author_Report_Collection
     */
    protected function _joinFields()
    {
        $this->_totals = new Varien_Object();

        $this->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('is_active');

        return $this;
    }
    
    public function addOrderedQtyForAuthorSold($frm = '', $to = '')
    {
		if(key_exists('report',$_SESSION)) {
			$author_id = $_SESSION['report']['authorid'];
		}
		else {
			$author_id ='';
		}

       $qtyOrderedTableName = $this->getTable('sales/order_item');
       $qtyOrderedFieldName = 'qty_ordered';

       $productIdTableName = $this->getTable('sales/order_item');
       $productIdFieldName = 'product_id';

		$productEntityIntTable = (string)Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_varchar';
		$authorTable = $this->getAuthorEntityTableName();
		$eavAttributeTable = $this->getTable('eav/attribute');

       $compositeTypeIds = Mage::getSingleton('catalog/product_type')->getCompositeTypes();

       $compositeTypeIds = Array (
					    '0' => 'grouped',
					    '1' => 'simple',
					    '2' => 'bundle'
						);

       $productTypes = $this->getConnection()->quoteInto(' AND (e.type_id NOT IN (?))', $compositeTypeIds);

       if ($frm != '' && $to != '') {
           $dateFilter = " AND `order`.created_at BETWEEN '{$frm}' AND '{$to}'";
       } else {
           $dateFilter = "";
       }

       $this->getSelect()->reset()->from(
          array('order_items' => $qtyOrderedTableName),
          array('ordered_qty' => "SUM(order_items.{$qtyOrderedFieldName})",'base_price_total' => "SUM(order_items.price)")
       );

       $order = Mage::getResourceSingleton('sales/order');

       //$stateAttr = $order->getAttribute('state');
       if(true){// ($stateAttr->getBackend()->isStatic()) {

           $_joinCondition = $this->getConnection()->quoteInto(
               'order.entity_id = order_items.order_id AND order.state<>?', Mage_Sales_Model_Order::STATE_CANCELED
           );
           $_joinCondition .= $dateFilter;

           $this->getSelect()->joinInner(
               array('order' => $this->getTable('sales/order')),
               $_joinCondition,
               array()
           );
       } else {

           $_joinCondition = 'order.entity_id = order_state.entity_id';
           $_joinCondition .= $this->getConnection()->quoteInto(' AND order_state.attribute_id=? ', $stateAttr->getId());
           $_joinCondition .= $this->getConnection()->quoteInto(' AND order_state.value<>? ', Mage_Sales_Model_Order::STATE_CANCELED);

           $this->getSelect()
               ->joinInner(
                   array('order' => $this->getTable('sales/order')),
                   'order.entity_id = order_items.order_id' . $dateFilter,
                   array())
               ->joinInner(
                   array('order_state' => $stateAttr->getBackend()->getTable()),
                   $_joinCondition,
                   array());
       }

       $this->getSelect()
           ->joinInner(array('e' => $this->getProductEntityTableName()),
               "e.entity_id = order_items.{$productIdFieldName}");
           //->having('ordered_qty > 0');

       $authorIdConcat = $author_id != '' ? " AND find_in_set($author_id,publish_author)" : "";

       $this->getSelect()
           ->joinInner(
               array('pei' => $productEntityIntTable),
               "e.entity_id = pei.entity_id",
               array())
           ->joinInner(
               array('ea' => $eavAttributeTable),
               "pei.attribute_id=ea.attribute_id AND ea.attribute_code='publish_author'",
               array())
           ->joinInner(
               array('avc' => $this->_authorVarcharTableName),
               "avc.entity_id=pei.value AND avc.attribute_id={$this->_authorNameAttributeId}",
               array("author_name" => "value", "author_id" => "avc.entity_id"))
            ->group('author_id');

       return $this;
   }
}
