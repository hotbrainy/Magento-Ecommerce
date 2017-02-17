<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Adminhtml_AttributeSplashController extends Fishpig_AttributeSplash_Controller_Adminhtml_Abstract
{
	/**
	 * Display a grid of splash groups
	 *
	 */
	public function indexAction()
	{
		$this->loadLayout();
		
		$this->_title('FishPig');
		$this->_title($this->__('Attribute Splash'));

		$this->_setActiveMenu('attributeSplash');
		$this->renderLayout();
	}
	
	/**
	 * Display the grid of splash groups without the container (header, footer etc)
	 * This is used to modify the grid via AJAX
	 *
	 */
	public function groupGridAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	/**
	 * Display the grid of splash pages without the container (header, footer etc)
	 * This is used to modify the grid via AJAX
	 *
	 */
	public function pageGridAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}
	
	/**
	 * Display the Extend tab
	 *
	 * @return void
	 */
	public function extendAction()
	{
		$block = $this->getLayout()
			->createBlock('attributeSplash/adminhtml_extend')
			->setModule('Fishpig_AttributeSplash')
			->setMedium('Add-On Tab')
			->setTemplate('large.phtml')
			->setLimit(4)
			->setPreferred(array('Fishpig_FSeo', 'Fishpig_AttributeSplash_Addon_QuickCreate', 'Fishpig_AttributeSplash_Addon_XmlSitemap', 'Fishpig_CrossLink', 'Fishpig_AttributeSplashPro', 'Fishpig_NoBots'));
			
		$this->getResponse()
			->setBody(
				$block->toHtml()
			);
	}
	
	/**
	 * Call an addon method
	 *
	 * @return void
	 */
	public function addonAction()
	{
		$module = $this->getRequest()->getParam('module');
		$data = $this->getRequest()->getPost($module);
		
		if (!$module || !$data) {
			return $this->_redirectReferer();
		}

		$helper = Mage::helper('attributeSplash_addon_' . $module);
		
		if (!$helper) {
			return $this->_redirectReferer();
		}
		
		try {
			if (($count = $helper->process($data)) > 0) {
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('%d page(s) were created.', $count));
			}
			else {
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('0 pages were created due to conflicts with existing pages.'));
			}
		}
		catch (Exception $e) {
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
		}
		
		return $this->_redirectReferer();
	}
}
