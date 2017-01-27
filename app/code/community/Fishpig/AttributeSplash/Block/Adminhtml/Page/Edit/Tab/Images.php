<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Page_Edit_Tab_Images extends Fishpig_AttributeSplash_Block_Adminhtml_Page_Edit_Tab_Abstract
{
	/**
	 * Retrieve Additional Element Types
	 *
	 * @return array
	*/
	protected function _getAdditionalElementTypes()
	{
		return array(
			'image' => Mage::getConfig()->getBlockClassName('attributeSplash/adminhtml_page_helper_image')
		);
	}

	/**
	 * Setup the form fields
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		parent::_prepareForm();
		
		$fieldset = $this->getForm()
			->addFieldset('splash_image', array('legend'=> $this->__('Images')));

		$this->_addElementTypes($fieldset);
		
		$fieldset->addField('image', 'image', array(
			'name' 	=> 'image',
			'label' => $this->__('Banner'),
			'title' => $this->__('Banner'),
		));
		
		$fieldset->addField('thumbnail', 'image', array(
			'name' 	=> 'thumbnail',
			'label' => $this->__('Logo'),
			'title' => $this->__('Logo'),
		));

		$this->getForm()->setValues($this->_getFormData());

		return $this;
	}
}
