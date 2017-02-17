<?php
class SteveB27_GoodReads_Model_Resource_Setup extends Mage_Eav_Model_Entity_Setup
{
	public function createAttributes(array $data)
	{
		$installer = $this;
		
		foreach($data as $attribute) {
			$entity	= $attribute['entity'];
			$code	= $attribute['code'];
			$label	= $attribute['label'];
			$type	= $attribute['type'];
			
			switch($type) {
				case 'int':
					$input = 'text';
					$type = 'int';
					$backend = '';
					$option = '';
					$source = '';
					break;
				case 'text':
					$input = 'text';
					$type = 'text';
					$backend = '';
					$option = '';
					$source = '';
					break;
				case 'textarea':
					$input = 'textarea';
					$type = 'text';
					$backend = '';
					$option = '';
					$source = '';
					break;
				case 'datetime':
					$input = 'date';
					$type = 'datetime';
					$backend = '';
					$option = '';
					$source = '';
					break;
				case 'select':
					$input = 'select';
					$type = 'text';
					$backend = 'eav/entity_attribute_backend_array';
					$option = $attribute['option'];
					$source = '';
					break;
				case 'multiselect':
					$input = 'multiselect';
					$type = 'text';
					$backend = 'eav/entity_attribute_backend_array';
					$option = $attribute['option'];
					$source = '';
					break;
				case 'boolean':
					$input = 'select';
					$type = 'int';
					$backend = 'eav/entity_attribute_backend_array';
					$option = '';
					$source = 'eav/entity_attribute_source_boolean';
					break;
				default:
					break;
			}
			
			$installer->addAttribute($entity, $code, array(
				'type'              => $type,
				'backend'           => $backend,
				'frontend'          => '',
				'label'             => $label,
				'input'             => $input,
				'class'             => '',
				'source'            => $source,
				'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
				'visible'           => true,
				'required'          => false,
				'user_defined'      => false,
				'default'           => '',
				'searchable'        => false,
				'filterable'        => false,
				'comparable'        => false,
				'visible_on_front'  => false,
				'unique'            => false,
				'apply_to'          => '',
				'is_configurable'   => false,
				'option'			=> $option,
			));
		}
	}
}