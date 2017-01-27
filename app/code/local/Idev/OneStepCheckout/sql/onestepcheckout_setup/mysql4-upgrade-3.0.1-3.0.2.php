<?php
$installer = $this;
$installer->startSetup();

$resource = Mage::getResourceModel('sales/order_collection');
if(!method_exists($resource, 'getEntity')){

    $table = $this->getTable('sales_flat_order');
    $query = 'ALTER TABLE `' . $table . '` ADD COLUMN `onestepcheckout_customercomment` TEXT CHARACTER SET utf8 DEFAULT NULL';
    $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
    try {
        $connection->query($query);
    } catch (Exception $e) {

    }
}

$installer->endSetup();
?>