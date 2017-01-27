<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Page_Edit_Tab_General extends Fishpig_AttributeSplash_Block_Adminhtml_Page_Edit_Tab_Abstract
{
	/**
	 * Setup the form fields
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		parent::_prepareForm();

		$fieldset = $this->getForm()
			->addFieldset('splash_page_information', array(
				'legend'=> $this->__('Page Information')
			));
		
		$fieldset->addField('display_name', 'text', array(
			'name' 		=> 'display_name',
			'label' 	=> $this->__('Name'),
			'title' 	=> $this->__('Name'),
			'required'	=> true,
			'class'		=> 'required-entry',
		));

		$field = $fieldset->addField('url_key', 'text', array(
			'name' => 'url_key',
			'label' => $this->__('URL Key'),
			'title' => $this->__('URL Key'),
		));

		$field->setRenderer(
			$this->getLayout()->createBlock('attributeSplash/adminhtml_form_field_urlkey')
				->setSplashType('page')
		);
		
		if ($page = Mage::registry('splash_page')) {
			$fieldset->addField('attribute_id', 'hidden', array(
				'name' 		=> 'attribute_id',
				'value' => $page->getAttributeId(),
			));
			
			$fieldset->addField('option_id', 'hidden', array(
				'name' 		=> 'option_id',
				'value' => $page->getOptionId(),
			));
		}
		
		$fieldset->addField('category_id', 'text', array(
			'name' => 'category_id',
			'label' => $this->__('Category ID'),
			'title' => $this->__('Category ID'),
			'note' => $this->__('Used to populate category filters in the layered navigation'),
		));

		if (!Mage::app()->isSingleStoreMode()) {
			$field = $fieldset->addField('store_ids', 'multiselect', array(
				'name' => 'store_ids[]',
				'label' => Mage::helper('cms')->__('Store View'),
				'title' => Mage::helper('cms')->__('Store View'),
				'required' => true,
				'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			));

			$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
			
			if ($renderer) {
				$field->setRenderer($renderer);
			}
		}
		else {
			if (($page = Mage::registry('splash_page')) !== null) {
				$page->setStoreId(Mage::app()->getStore()->getId());
			}
		}

		$fieldset->addField('is_enabled', 'select', array(
			'name' => 'is_enabled',
			'title' => $this->__('Enabled'),
			'label' => $this->__('Enabled'),
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));
		
		$fieldset->addField('is_featured', 'select', array(
			'name' => 'is_featured',
			'title' => $this->__('Featured'),
			'label' => $this->__('Featured'),
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));

		$this->getForm()->setValues($this->_getFormData());

		return $this;
	}
}
