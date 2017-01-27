<?php
$type = Mage::getModel('eav/entity_type')->loadByCode(SteveB27_Publish_Model_Author::ENTITY);
$attributes = Mage::getResourceModel('eav/entity_attribute_collection')->setEntityTypeFilter($type);
$tableAttributes = Mage::getResourceModel('publish/attribute_collection');
$broke = array();

foreach($tableAttributes as $attribute) {
	if($attribute->getIsVisible() == '') {
		$attribute->setIsVisible(1);
		$attribute->setIsWysiwygEnabled(0);
		$attribute->setPosition(0);
		$attribute->save();
	}
}
