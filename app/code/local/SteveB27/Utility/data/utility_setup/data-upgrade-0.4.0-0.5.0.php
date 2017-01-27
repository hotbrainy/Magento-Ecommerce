<?php
$installer = $this;

$attribute_id = Mage::getResourceModel('eav/entity_attribute')
    ->getIdByCode('catalog_product', 'book_series_2');

$resource = Mage::getModel('core/resource');

$int_table = $resource->getTableName('catalog_product_entity_int');
$dec_table = $resource->getTableName('catalog_product_entity_decimal');

$read_connection = Mage::getModel('core/resource')->getConnection('utility_read');
$write_connection = Mage::getModel('core/resource')->getConnection('utility_write');

$select = "SELECT * FROM " . $int_table . " WHERE attribute_id = " . $attribute_id;

$values = $read_connection->fetchAll($select);

$updated = array();

foreach($values as $data) {
	$value_id = $data['value_id'];
	$data['value_id'] = null;
	$write_connection->insert($dec_table,$data);
	$write_connection->delete($int_table,"value_id=".$value_id);
}