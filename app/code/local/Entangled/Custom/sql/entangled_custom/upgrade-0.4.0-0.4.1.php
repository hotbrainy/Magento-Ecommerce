<?php
/** @var Mage_Eav_Model_Entity_Setup $installer */
$installer = $this;
$installer->startSetup();

$eavAttribute = Mage::getModel("catalog/product")->getResource()->getAttribute("name");
$labels= $eavAttribute->getStoreLabels();
$newLabel = "Title";

foreach($labels as $key => $label){
    $labels[$key] = $newLabel;
}
$eavAttribute
    ->setData("frontend_label",$newLabel)
    ->setStoreLabels($labels)
    ->save();

$installer->endSetup();