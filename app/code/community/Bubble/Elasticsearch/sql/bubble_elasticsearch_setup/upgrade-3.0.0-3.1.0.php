<?php
/**
 * @var $installer Mage_Catalog_Model_Resource_Setup
 */
$installer = $this;
$installer->startSetup();

$pageIds = Mage::getModel('cms/page')->getCollection()
    ->addFieldToFilter('identifier', array('in' => array('enable-cookies', 'service-unavailable', 'no-route')))
    ->getAllIds();
if (!empty($pageIds)) {
    $installer->setConfigData('elasticsearch/cms/excluded_pages', implode(',', $pageIds));
}

$installer->endSetup();