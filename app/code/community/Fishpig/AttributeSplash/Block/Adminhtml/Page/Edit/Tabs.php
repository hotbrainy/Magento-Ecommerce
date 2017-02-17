<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Page_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	/**
	 * Init the tabs block
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('splash_page_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Splash Page Information'));
	}
	
	/**
	 * Add tabs to the block
	 *
	 * @return $this
	 */
	protected function _beforeToHtml()
	{
		$tabs = array(
			'general' => 'Page Information',
			'attributes' => 'Attributes',
			'content' => 'Content',
			'images' => 'Images',
			'design' => 'Design',
			'meta' => 'Meta Data',
		);
		
		foreach($tabs as $alias => $label) {
			$this->addTab($alias, array(
				'label' => $this->__($label),
				'title' => $this->__($label),
				'content' => $this->getLayout()->createBlock('attributeSplash/adminhtml_page_edit_tab_' . $alias)->toHtml(),
			));
		}
		
		if ($page = Mage::registry('splash_page')) {
			if ($page->hasAvailableCustomFields()) {
				$this->addTab('custom_fields', array(
					'label' => $this->__('Custom Fields'),
					'title' => $this->__('Custom Fields'),
					'content' => $this->getLayout()->createBlock('attributeSplash/adminhtml_page_edit_tab_customfields')->toHtml(),
				));
			}
		}
		
		Mage::dispatchEvent('attributesplash_adminhtml_page_edit_tabs', array('tabs' => $this));
		
		return parent::_beforeToHtml();
	}
}
