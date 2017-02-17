<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Group_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		$this->setId('splash_group_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Splash Group Information'));
	}
	
	protected function _beforeToHtml()
	{
		$tabs = array(
			'general' => 'Page Information',
			'content' => 'Content',
			'design' => 'Design',
			'meta' => 'Meta Data',
		);
		
		foreach($tabs as $alias => $label) {
			$this->addTab($alias, array(
				'label' => $this->__($label),
				'title' => $this->__($label),
				'content' => $this->getLayout()->createBlock('attributeSplash/adminhtml_group_edit_tab_' . $alias)->toHtml(),
			));
		}

		return parent::_beforeToHtml();
	}
}
