<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Page_Edit_Tab_Attributes extends Fishpig_AttributeSplash_Block_Adminhtml_Page_Edit_Tab_Abstract
{
	/**
	 * Add the design elements to the form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		parent::_prepareForm();

		$fieldset = $this->getForm()->addFieldset('splash_attributes', array(
			'legend'=> $this->helper('adminhtml')->__('Attributes'),
			'class' => 'fieldset-wide',
		));

		$page = Mage::registry('splash_page');

		$fieldset->addField('attribute_id', 'select', array(
			'name' => 'attribute_id',
			'label' => $this->__('Attribute'),
			'title' => $this->__('Attribute'),
			'values' => Mage::getSingleton('attributeSplash/system_config_source_attribute_splashable')->toOptionArray(true),
			'required' => true,
			'disabled' => !is_null($page),
		));
		
		$fieldset->addField('option_id', 'select', array(
			'name' => 'option_id',
			'label' => $this->__('Option'),
			'title' => $this->__('Option'),
			'values' => $this->_getPageOptionValues($page),
			'required' => true,
			'disabled' => !is_null($page),
		));
		
		$this->getForm()->setValues($this->_getFormData());
		
		return $this;
	}
	
	/**
	 * Retrieve the option values for the page
	 * 
	 * @param Fishpig_AttributeSplash_Model_Page $page = null
	 * @return array
	 */
	protected function _getPageOptionValues($page = null)
	{
		if (is_null($page)) {
			return array();
		}
		
		$option = Mage::getResourceModel('eav/entity_attribute_option_collection')
			->setStoreFilter($page->getStoreId())
			->addFieldToFilter('main_table.option_id', $page->getOptionId())
			->setPageSize(1)
			->load()
			->getFirstItem();
		
		if (!$option->getId()) {
			return array();
		}
		
		return array(array(
			'value' => $page->getOptionId(),
			'label' => $option->getValue(),
		));
	}
}
