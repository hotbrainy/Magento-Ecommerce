<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_AttributeSplash_Block_Adminhtml_Page_Edit_Tab_Abstract extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Generate the form object
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('splash_');
        $form->setFieldNameSuffix('splash');
        
		$this->setForm($form);

		return parent::_prepareForm();
	}
	
	/**
	 * Retrieve the data used for the form
	 *
	 * @return array
	 */
	protected function _getFormData()
	{
		if ($page = Mage::registry('splash_page')) {
			return $page->getData();
		}
		
		return array('is_enabled' => 1, 'store_ids' => array(0), 'include_in_menu' => 1);
	}
}
