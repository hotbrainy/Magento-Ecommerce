<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Group_Edit_Tab_Seo extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('splash_');
        $form->setFieldNameSuffix('splash');
	
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('splash_seo', array('legend'=> $this->__('Search Engine Optimizations')));
		
		$fieldset->addField('url_key', 'text', array(
			'name' => 'url_key',
			'label' => $this->__('URL Key'),
			'title' => $this->__('URL Key'),
			'note'	=> $this->__('If left empty the URL key will be automatically generated based on the name field'),
		));
		
		$fieldset->addField('page_title', 'text', array(
			'name' => 'page_title',
			'label' => $this->__('Page Title'),
			'title' => $this->__('Page Title'),
		));
		
		$fieldset->addField('meta_description', 'editor', array(
			'name' => 'meta_description',
			'label' => $this->__('Meta Description'),
			'title' => $this->__('Meta Description'),
			'style' => 'width:98%; height:110px;',
		));
		
		$fieldset->addField('meta_keywords', 'editor', array(
			'name' => 'meta_keywords',
			'label' => $this->__('Meta Keywords'),
			'title' => $this->__('Meta Keywords'),
			'style' => 'width:98%; height:110px;',
		));
		
		if ($splashGroup = Mage::registry('splash_group')) {
			$form->setValues($splashGroup->getData());
		}

		return parent::_prepareForm();
	}
}
