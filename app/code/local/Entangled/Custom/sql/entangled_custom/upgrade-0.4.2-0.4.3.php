<?php
$installer = $this;
$installer->startSetup();

$conn = $installer->getConnection();
$attribute_id = $installer->getAttributeId('catalog_product', 'book_series_length');

$stmt = $conn->prepare("UPDATE mgti_eav_attribute SET frontend_input = 'multiselect',backend_type = 'varchar',is_user_defined = 1 WHERE mgti_eav_attribute.attribute_id = :attribute_id");
$stmt->bindParam(':attribute_id', $attribute_id);
$stmt->execute();

$stmt = $conn->prepare("INSERT INTO mgti_catalog_product_entity_varchar (entity_type_id, attribute_id, store_id, entity_id, value) SELECT entity_type_id, attribute_id, store_id, entity_id, value FROM mgti_catalog_product_entity_text WHERE attribute_id = :attribute_id");
$stmt->bindParam(':attribute_id', $attribute_id);
$stmt->execute();

$stmt = $conn->prepare("DELETE FROM mgti_catalog_product_entity_text WHERE attribute_id = :attribute_id");
$stmt->bindParam(':attribute_id', $attribute_id);
$stmt->execute();

$installer->endSetup();