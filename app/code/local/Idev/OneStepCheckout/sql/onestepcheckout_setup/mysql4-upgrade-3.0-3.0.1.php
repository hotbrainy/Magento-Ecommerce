<?php
$installer = $this;
$installer->startSetup();

$resource = Mage::getResourceModel('sales/order_collection');
if(!method_exists($resource, 'getEntity')){

    $table = $this->getTable('sales_flat_order');
    $query = 'ALTER TABLE `' . $table . '` ADD COLUMN `onestepcheckout_customerfeedback` TEXT CHARACTER SET utf8 DEFAULT NULL';
    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
    $connection->query($query);

} else {

    $attribute = array(
        'entity_type_id'  => $installer->getEntityTypeId('order'),
        'attribute_code'  => 'onestepcheckout_customerfeedback',
        'backend_type'    => 'text',
        'frontend_input'  => 'textarea',
        'is_global'       => '1',
        'is_visible'      => '1',
        'is_required'     => '0',
        'is_user_defined' => '0'
    );

    $newAttribute = new Mage_Eav_Model_Entity_Attribute();
    $newAttribute->loadByCode($attribute['entity_type_id'], $attribute['attribute_code'])
              ->setStoreId(0)
              ->addData($attribute)
              ->save();

}

$installer->endSetup();
?>