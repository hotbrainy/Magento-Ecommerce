<?php

$installer = $this;
$installer->startSetup();



//Add attributes

$installer->addAttribute('catalog_category', 'umm_cat_block_right', array(
	'group'				=> 'Menu',
	'label'				=> 'Block Right',
	'note'				=> "This field is applicable only for top-level categories.",
	'type'				=> 'text',
	'input'				=> 'textarea',
	'visible'			=> true,
	'required'			=> false,
	'backend'			=> '',
	'frontend'			=> '',
	'searchable'		=> false,
	'filterable'		=> false,
	'comparable'		=> false,
	'user_defined'		=> true,
	'visible_on_front'	=> true,
	'wysiwyg_enabled'	=> true,
	'is_html_allowed_on_front'	=> true,
	'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));
$installer->addAttribute('catalog_category', 'umm_cat_block_proportions', array(
	'group'				=> 'Menu',
	'label'				=> 'Proportions: Subcategories / Block Right',
	'note'				=> "Proportions between block of subcategories and Block Right. This field is applicable only for top-level categories.",
	'type'				=> 'varchar',
	'input'				=> 'select',
	'source'			=> 'ultramegamenu/category_attribute_source_block_proportions',
	'visible'			=> true,
	'required'			=> false,
	'backend'			=> '',
	'frontend'			=> '',
	'searchable'		=> false,
	'filterable'		=> false,
	'comparable'		=> false,
	'user_defined'		=> true,
	'visible_on_front'	=> true,
	'wysiwyg_enabled'	=> false,
	'is_html_allowed_on_front'	=> false,
	'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installer->addAttribute('catalog_category', 'umm_cat_block_top', array(
	'group'				=> 'Menu',
	'label'				=> 'Block Top',
	'type'				=> 'text',
	'input'				=> 'textarea',
	'visible'			=> true,
	'required'			=> false,
	'backend'			=> '',
	'frontend'			=> '',
	'searchable'		=> false,
	'filterable'		=> false,
	'comparable'		=> false,
	'user_defined'		=> true,
	'visible_on_front'	=> true,
	'wysiwyg_enabled'	=> true,
	'is_html_allowed_on_front'	=> true,
	'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installer->addAttribute('catalog_category', 'umm_cat_block_bottom', array(
	'group'				=> 'Menu',
	'label'				=> 'Block Bottom',
	'type'				=> 'text',
	'input'				=> 'textarea',
	'visible'			=> true,
	'required'			=> false,
	'backend'			=> '',
	'frontend'			=> '',
	'searchable'		=> false,
	'filterable'		=> false,
	'comparable'		=> false,
	'user_defined'		=> true,
	'visible_on_front'	=> true,
	'wysiwyg_enabled'	=> true,
	'is_html_allowed_on_front'	=> true,
	'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));

$installer->addAttribute('catalog_category', 'umm_cat_label', array(
	'group'				=> 'Menu',
	'label'				=> 'Category Label',
	'note'				=> "Labels have to be defined in menu settings",
	'type'				=> 'varchar',
	'input'				=> 'select',
	'source'			=> 'ultramegamenu/category_attribute_source_categorylabel',
							//'catalog/product_attribute_source_countryofmanufacture',
	'visible'			=> true,
	'required'			=> false,
	'backend'			=> '',
	'frontend'			=> '',
	'searchable'		=> false,
	'filterable'		=> false,
	'comparable'		=> false,
	'user_defined'		=> true,
	'visible_on_front'	=> true,
	'wysiwyg_enabled'	=> false,
	'is_html_allowed_on_front'	=> false,
	'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
));



$installer->endSetup();