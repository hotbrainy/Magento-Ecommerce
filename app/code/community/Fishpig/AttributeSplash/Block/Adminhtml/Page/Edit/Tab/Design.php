<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Page_Edit_Tab_Design extends Fishpig_AttributeSplash_Block_Adminhtml_Page_Edit_Tab_Abstract
{
	/**
	 * Add the design elements to the form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		parent::_prepareForm();

		$fieldset = $this->getForm()->addFieldset('splash_design_menu', array(
			'legend'=> $this->helper('adminhtml')->__('Menu'),
			'class' => 'fieldset-wide',
		));
		
		$fieldset->addField('include_in_menu', 'select', array(
			'name' => 'include_in_menu',
			'label' => $this->__('Include in Navigation Menu'),
			'title' => $this->__('Include in Navigation Menu'),
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));
		
		$fieldset = $this->getForm()->addFieldset('splash_design_page_layout', array(
			'legend'=> $this->helper('adminhtml')->__('Page Layout'),
			'class' => 'fieldset-wide',
		));

		$fieldset->addField('page_layout', 'select', array(
			'name' => 'page_layout',
			'label' => $this->__('Page Layout'),
			'title' => $this->__('Page Layout'),
			'values' => Mage::getSingleton('attributeSplash/system_config_source_layout')->toOptionArray(),
		));
		
		$fieldset->addField('layout_update_xml', 'editor', array(
			'name' => 'layout_update_xml',
			'label' => $this->__('Layout Update XML'),
			'title' => $this->__('Layout Update XML'),
			'style' => 'width:600px;',
		));

		$fieldset = $this->getForm()->addFieldset('splash_design_display_settings', array(
			'legend'=> $this->helper('adminhtml')->__('Display Settings'),
			'class' => 'fieldset-wide',
		));
		
		$fieldset->addField('display_mode', 'select', array(
			'name' => 'display_mode',
			'label' => $this->__('Display Mode'),
			'title' => $this->__('Display Mode'),
			'values' => Mage::getModel('catalog/category_attribute_source_mode')->getAllOptions(),
		));
		
		$fieldset->addField('cms_block', 'select', array(
			'name' => 'cms_block',
			'label' => $this->__('CMS Block'),
			'title' => $this->__('CMS Block'),
			'values' => Mage::getModel('catalog/category_attribute_source_page')->getAllOptions(),
		));

		$this->getForm()->setValues($this->_getFormData());
		
		return $this;
	}
}
