<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Group_Edit_Tab_Meta extends Fishpig_AttributeSplash_Block_Adminhtml_Group_Edit_Tab_Abstract
{
	/**
	 * Add the meta fields to the form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		parent::_prepareForm();
		
		$fieldset = $this->getForm()->addFieldset('splash_page_meta', array(
			'legend'=> $this->helper('adminhtml')->__('Meta Data'),
			'class' => 'fieldset-wide',
		));


		$fieldset->addField('page_title', 'text', array(
			'name' => 'page_title',
			'label' => $this->__('Page Title'),
			'title' => $this->__('Page Title'),
		));
		
		$fieldset->addField('meta_description', 'editor', array(
			'name' => 'meta_description',
			'label' => $this->__('Description'),
			'title' => $this->__('Description'),
			'style' => 'width:98%; height:110px;',
		));
		
		$fieldset->addField('meta_keywords', 'editor', array(
			'name' => 'meta_keywords',
			'label' => $this->__('Keywords'),
			'title' => $this->__('Keywords'),
			'style' => 'width:98%; height:110px;',
		));
		
		$this->getForm()->setValues($this->_getFormData());
		
		return $this;
	}
}
