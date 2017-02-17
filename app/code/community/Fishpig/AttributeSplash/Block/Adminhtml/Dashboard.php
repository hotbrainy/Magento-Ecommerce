<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Dashboard extends Mage_Adminhtml_Block_Widget_Tabs
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setId('splash_dashboard_tabs');
        $this->setDestElementId('splash_tab_content');
		$this->setTitle($this->__('Attribute Splash Pages'));
		$this->setTemplate('widget/tabshoriz.phtml');
	}
	
	protected function _prepareLayout()
	{
		$tabs = array(
			'group' => 'Groups',
			'page' => 'Pages',
		);
		
		$_layout = $this->getLayout();
		
		foreach($tabs as $alias => $label) {
			$this->addTab($alias, array(
				'label'     => Mage::helper('catalog')->__($label),
				'content'   => $_layout->createBlock('attributeSplash/adminhtml_' . $alias)->toHtml(),
				'active'    => $alias === 'page',
			));
		}

		Mage::dispatchEvent('attributesplash_dashboard_tabs_prepare_layout', array('tabs' => $this));
		
		if (!isset($this->_tabs['xmlsitemap'])) {
			$this->addTab('xmlsitemap', array(
				'label' => Mage::helper('catalog')->__('XML Sitemap'),
				'content' => $_layout->createBlock('attributeSplash/adminhtml_extend')
					->setTemplate('large.phtml')
					->setModule('Fishpig_AttributeSplash')
					->setMedium('XML Sitemap Tab')
					->setLimit(1)
					->setPreferred(array('Fishpig_AttributeSplash_Addon_XmlSitemap'))
					->toHtml(),
			));
		}

		if ($extend = $_layout->createBlock('attributeSplash/adminhtml_extend')) {
			$extend->setNameInLayout('fishpig.extend')
				->setTabLabel($this->__('Add-Ons'))
				->setTabUrl('*/*/extend');
				
			$this->addTab('extend', $extend);
		}
				
		return parent::_prepareLayout();
	}
}
