<?php

$installer = $this;
$installer->startSetup();


# Inserting tables
$installer->run("

DROP TABLE IF EXISTS `{$this->getTable('aw_ed_file_entity')}`;
CREATE TABLE `{$this->getTable('aw_ed_file_entity')}` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `increment_id` varchar(50) NOT NULL default '',
  `product_id` int(10) unsigned default NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_EXTRADOWNLOADS_ENTITY_ENTITY_TYPE` (`entity_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Customer Entityies';

DROP TABLE IF EXISTS `{$this->getTable('aw_ed_file_entity_datetime')}`;
CREATE TABLE `{$this->getTable('aw_ed_file_entity_datetime')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_EXTRADOWNLOADS_DATETIME_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_EXTRADOWNLOADS_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_EXTRADOWNLOADS_DATETIME_STORE` (`store_id`),
  KEY `FK_EXTRADOWNLOADS_DATETIME_ENTITY` (`entity_id`),
  CONSTRAINT `FK_EXTRADOWNLOADS_DATETIME_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_DATETIME_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('aw_ed_file_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_DATETIME_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_DATETIME_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$this->getTable('aw_ed_file_entity_decimal')}`;
CREATE TABLE `{$this->getTable('aw_ed_file_entity_decimal')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_EXTRADOWNLOADS_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_EXTRADOWNLOADS_DECIMAL_ATTRIBUTE` (`attribute_id`),
  KEY `FK_EXTRADOWNLOADS_DECIMAL_STORE` (`store_id`),
  KEY `FK_EXTRADOWNLOADS_DECIMAL_ENTITY` (`entity_id`),
  CONSTRAINT `FK_EXTRADOWNLOADS_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_DECIMAL_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('aw_ed_file_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_DECIMAL_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_DECIMAL_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$this->getTable('aw_ed_file_entity_int')}`;
CREATE TABLE `{$this->getTable('aw_ed_file_entity_int')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_EXTRADOWNLOADS_INT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_EXTRADOWNLOADS_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_EXTRADOWNLOADS_INT_STORE` (`store_id`),
  KEY `FK_EXTRADOWNLOADS_INT_ENTITY` (`entity_id`),
  CONSTRAINT `FK_EXTRADOWNLOADS_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_INT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('aw_ed_file_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_INT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_INT_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$this->getTable('aw_ed_file_entity_text')}`;
CREATE TABLE `{$this->getTable('aw_ed_file_entity_text')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_EXTRADOWNLOADS_TEXT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_EXTRADOWNLOADS_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_EXTRADOWNLOADS_TEXT_STORE` (`store_id`),
  KEY `FK_EXTRADOWNLOADS_TEXT_ENTITY` (`entity_id`),
  CONSTRAINT `FK_EXTRADOWNLOADS_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_TEXT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('aw_ed_file_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_TEXT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_TEXT_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{$this->getTable('aw_ed_file_entity_varchar')}`;
CREATE TABLE `{$this->getTable('aw_ed_file_entity_varchar')}` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_EXTRADOWNLOADS_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_EXTRADOWNLOADS_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_EXTRADOWNLOADS_VARCHAR_STORE` (`store_id`),
  KEY `FK_EXTRADOWNLOADS_VARCHAR_ENTITY` (`entity_id`),
  CONSTRAINT `FK_EXTRADOWNLOADS_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_VARCHAR_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('aw_ed_file_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_VARCHAR_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_EXTRADOWNLOADS_VARCHAR_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

# Insert product attributes
$setup->addAttribute('catalog_product', 'extradownloads_enabled', array(
        'backend_type'  => 'int',
        'is_global'     => 0,
        'is_visible'    => 0,
        'required'      => false,
        'user_defined'  => false,
        'default'       => 0,
        'visible_on_front' => false
    ));

$setup->addAttribute('catalog_product', 'extradownloads_title', array(
        'backend_type'  => 'varchar',
        'is_global'     => 0,
        'is_visible'    => 0,
        'required'      => false,
        'user_defined'  => false,
        'default'       => null,
        'visible_on_front' => false
    ));

if (Mage::helper('extradownloads')->checkVersion('1.4.0.0')){
    $setup->updateAttribute('catalog_product', 'extradownloads_enabled', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
    $setup->updateAttribute('catalog_product', 'extradownloads_enabled', 'is_visible', 0);
    $setup->updateAttribute('catalog_product', 'extradownloads_title', 'is_global', Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE);
    $setup->updateAttribute('catalog_product', 'extradownloads_title', 'is_visible', 0);
}

# Create new entity_type
$setup->addEntityType('extradownloads_file', array(
        'entity_model'=>'extradownloads/file',
        'table'=>'extradownloads/file',
        'increment_model'=>'eav/entity_increment_numeric',
    ));

# Insert special attributes 
$setup->addAttribute('extradownloads_file', 'title', array(
        'backend_type'  => 'varchar',
        'global'        => false,
        'visible'       => false,
        'required'      => false,
        'user_defined'  => false,
        'default'       => null,
        'visible_on_front' => false
    ));

$setup->addAttribute('extradownloads_file', 'visible', array(
        'backend_type'  => 'int',
        'global'        => false,
        'visible'       => false,
        'required'      => false,
        'user_defined'  => false,
        'default'       => 1,
        'visible_on_front' => false
    ));

$setup->addAttribute('extradownloads_file', 'sort_order', array(
        'backend_type'  => 'varchar',
        'global'        => false,
        'visible'       => false,
        'required'      => false,
        'user_defined'  => false,
        'default'       => null,
        'visible_on_front' => false
    ));

$setup->addAttribute('extradownloads_file', 'file', array(
        'backend_type'  => 'varchar',
        'global'        => false,
        'visible'       => false,
        'required'      => false,
        'user_defined'  => false,
        'default'       => null,
        'visible_on_front' => false
    ));

$setup->addAttribute('extradownloads_file', 'url', array(
        'backend_type'  => 'varchar',
        'global'        => false,
        'visible'       => false,
        'required'      => false,
        'user_defined'  => false,
        'default'       => null,
        'visible_on_front' => false
    ));

$setup->addAttribute('extradownloads_file', 'type', array(
        'backend_type'  => 'varchar',
        'global'        => false,
        'visible'       => false,
        'required'      => false,
        'user_defined'  => false,
        'default'       => null,
        'visible_on_front' => false
    ));

$setup->addAttribute('extradownloads_file', 'downloads', array(
        'backend_type'  => 'varchar',
        'global'        => false,
        'visible'       => false,
        'required'      => false,
        'user_defined'  => false,
        'default'       => null,
        'visible_on_front' => false
    ));

$installer->endSetup();
