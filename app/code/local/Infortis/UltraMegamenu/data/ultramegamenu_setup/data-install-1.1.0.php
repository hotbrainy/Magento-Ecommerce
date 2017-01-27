<?php

$installer = $this;
$installer->startSetup();



//Add attributes

$installer->addAttribute('catalog_category', 'umm_dd_type', array(
	'group'					=> 'Menu',
	'label'					=> 'Submenu Type',
	'note'					=> 'If category has subcategories, choose how subcategories should be displayed. For details refer to the user guide, chapter: 13. Menu',

	'backend'				=> '',
	'type'					=> 'varchar',
	'frontend'				=> '',
	'input'					=> 'select',
	//'input_renderer'		=> '',
	//'frontend_class'		=> '',
	'source'				=> 'ultramegamenu/category_attribute_source_dropdown_type',

	'user_defined'			=> true,
	'required'				=> false,
	'visible'				=> true,
	'searchable'			=> false,
	'filterable'			=> false,
	'comparable'			=> false,
	'visible_on_front'		=> true,
	'wysiwyg_enabled'		=> false,
	'is_html_allowed_on_front' => false,
	'global'				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'sort_order'			=> 110,
));

$installer->addAttribute('catalog_category', 'umm_dd_width', array(
	'group'					=> 'Menu',
	'label'					=> 'Drop-down Width',
	'note'					=> "Override default width of the drop-down box. Enter value in pixels, e.g. 150px, or as a percentage of the containing block's width, e.g. 200%.",

	'backend'				=> '',
	'type'					=> 'varchar',
	'frontend'				=> '',
	'input'					=> 'text',
	//'input_renderer'		=> '',
	//'frontend_class'		=> '',

	'user_defined'			=> true,
	'required'				=> false,
	'visible'				=> true,
	'searchable'			=> false,
	'filterable'			=> false,
	'comparable'			=> false,
	'visible_on_front'		=> true,
	'wysiwyg_enabled'		=> false,
	'is_html_allowed_on_front' => false,
	'global'				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'sort_order'			=> 120,
));

$installer->addAttribute('catalog_category', 'umm_dd_proportions', array(
	'group'					=> 'Menu',
	'label'					=> 'Drop-down Content Proportions',
	'note'					=> 'Proportions between sections of drop-down box: Left Block, subcategories, Right Block. For each section, enter value in grid units (number between 0 and 12). Sum of the grid units entered for all three sections has to be equal 12.',

	'backend'				=> 'ultramegamenu/category_attribute_backend_grid_columns',
	'type'					=> 'varchar',
	'frontend'				=> '',
	'input'					=> 'text',
	'input_renderer'		=> 'ultramegamenu/category_attribute_helper_grid_columns',
	//'frontend_class'		=> '',

	'user_defined'			=> true,
	'required'				=> false,
	'visible'				=> true,
	'searchable'			=> false,
	'filterable'			=> false,
	'comparable'			=> false,
	'visible_on_front'		=> true,
	'wysiwyg_enabled'		=> false,
	'is_html_allowed_on_front' => false,
	'global'				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'sort_order'			=> 130,
));

$installer->addAttribute('catalog_category', 'umm_dd_columns', array(
	'group'					=> 'Menu',
	'label'					=> 'Number of Columns With Subcategories',
	'note'					=> "Applicable only for categories with Submenu Type 'Mega drop-down'. E.g. select 3 to display subcategories in three columns. Default value is 4.",

	'backend'				=> '',
	'type'					=> 'int',
	'frontend'				=> '',
	'input'					=> 'select',
	//'input_renderer'		=> '',
	//'frontend_class'		=> '',
	'source'				=> 'ultramegamenu/category_attribute_source_dropdown_columns',

	'user_defined'			=> true,
	'required'				=> false,
	'visible'				=> true,
	'searchable'			=> false,
	'filterable'			=> false,
	'comparable'			=> false,
	'visible_on_front'		=> true,
	'wysiwyg_enabled'		=> false,
	'is_html_allowed_on_front' => false,
	'global'				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'sort_order'			=> 140,
));

$installer->addAttribute('catalog_category', 'umm_dd_blocks', array(
	'group'					=> 'Menu',
	'label'					=> 'Category Blocks',
	'note'					=> '',

	'backend'				=> 'ultramegamenu/category_attribute_backend_dropdown_blocks',
	'type'					=> 'text',
	'frontend'				=> '',
	'input'					=> 'textarea',
	'input_renderer'		=> 'ultramegamenu/category_attribute_helper_dropdown_blocks',
	//'frontend_class'		=> '',

	'user_defined'			=> true,
	'required'				=> false,
	'visible'				=> true,
	'searchable'			=> false,
	'filterable'			=> false,
	'comparable'			=> false,
	'visible_on_front'		=> true,
	'wysiwyg_enabled'		=> true,
	'is_html_allowed_on_front' => true,
	'global'				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'sort_order'			=> 300,
));

$installer->addAttribute('catalog_category', 'umm_cat_label', array(
	'group'				=> 'Menu',
	'label'				=> 'Category Label',
	'note'				=> "Labels have to be defined in menu settings",

	'backend'			=> '',
	'type'				=> 'varchar',
	'frontend'			=> '',
	'input'				=> 'select',
	//'input_renderer'	=> '',
	//'frontend_class'	=> '',
	'source'			=> 'ultramegamenu/category_attribute_source_categorylabel',

	'user_defined'		=> true,
	'required'			=> false,
	'visible'			=> true,
	'searchable'		=> false,
	'filterable'		=> false,
	'comparable'		=> false,
	'visible_on_front'	=> true,
	'wysiwyg_enabled'	=> false,
	'is_html_allowed_on_front' => false,
	'global'			=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'sort_order'		=> 500,
));

$installer->addAttribute('catalog_category', 'umm_cat_target', array(
	'group'					=> 'Menu',
	'label'					=> 'Custom URL',
	'note'					=> "Enter hash (#) to make this category not clickable. To create a custom link (which will replace category link), enter custom URL path. Path will be appended to store's base URL to create a new link. Leave this field empty if no changes are needed.",

	'backend'				=> '',
	'type'					=> 'varchar',
	'frontend'				=> '',
	'input'					=> 'text',
	//'input_renderer'		=> '',
	//'frontend_class'		=> '',

	'user_defined'			=> true,
	'required'				=> false,
	'visible'				=> true,
	'searchable'			=> false,
	'filterable'			=> false,
	'comparable'			=> false,
	'visible_on_front'		=> true,
	'wysiwyg_enabled'		=> false,
	'is_html_allowed_on_front' => false,
	'global'				=> Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
	'sort_order'			=> 600,
));



$installer->endSetup();