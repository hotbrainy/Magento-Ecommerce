<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_GroupController extends Mage_Core_Controller_Front_Action
{
	public function viewAction()
	{
		if ($splashGroup = $this->_initSplashGroup()) {
			$this->_applyCustomViewLayout($splashGroup);

			if ($rootBlock = $this->getLayout()->getBlock('root')) {
				$rootBlock->addBodyClass('splash-group-' . $splashGroup->getId());
			}
			
			if ($headBlock = $this->getLayout()->getBlock('head')) {
				if ($title = $splashGroup->getPageTitle()) {			
					$headBlock->setTitle($title);
				}
				else {
					$this->_title($splashGroup->getDisplayName());
				}

				if ($description = $splashGroup->getMetaDescription()) {
					$headBlock->setDescription($description);
				}
				
				if ($keywords = $splashGroup->getMetaKeywords()) {
					$headBlock->setKeywords($keywords);
				}
				
				$headBlock->addItem('link_rel', $splashGroup->getUrl(), 'rel="canonical"');
			}
			
			if ($breadBlock = $this->getLayout()->getBlock('breadcrumbs')) {
				$breadBlock->addCrumb('home', array('label' => $this->__('Home'), 'title' => $this->__('Home'), 'link' => Mage::getUrl()));
				$breadBlock->addCrumb('splash_group', array('label' => $splashGroup->getName(), 'title' => $splashGroup->getName()));
			}

			$this->renderLayout();
		}
		else {
			$this->_forward('noRoute');
		}
	}
	
	/**
	 * Apply custom layout handles to the splash page
	 *
	 * @param Fishpig_AttribtueSplash_Model_Page $splashPage
	 * @return Fishpig_AttribtueSplash_PageController
	 */
	protected function _applyCustomViewLayout(Fishpig_AttributeSplash_Model_Group $splashGroup)
	{
		$update = $this->getLayout()->getUpdate();
		
		$update->addHandle('default');
		$this->addActionLayoutHandles();
		$update->addHandle('attributesplash_group_view_' . $splashGroup->getId());
		$update->addHandle('attributesplash_group_view_' . $splashGroup->getAttributeCode());

		$this->loadLayoutUpdates();
		
		$update->addUpdate($splashGroup->getLayoutUpdateXml());

		$this->generateLayoutXml()->generateLayoutBlocks();

		if ($splashGroup->getPageLayout()) {
			$this->getLayout()->helper('page/layout')->applyTemplate($splashGroup->getPageLayout());
		}
		else if ($pageLayout = Mage::getStoreConfig('attributeSplash/group/template')) {
			$this->getLayout()->helper('page/layout')->applyTemplate($pageLayout);
		}
		
		$this->_isLayoutLoaded = true;

		return $this;	
	}

	/**
	 * Initialise the Splash Gourp model
	 *
	 * @return false|Fishpig_AttributeSplash_Model_Gourp
	 */
	protected function _initSplashGroup()
	{
		if (($group = Mage::registry('splash_group')) !== null) {
			return $group;
		}
		
		$group = Mage::getModel('attributeSplash/group')->load(
			(int) $this->getRequest()->getParam('id', false)
		);
		
		if (!$group->getId()) {
			return false;
		}
		
		Mage::register('splash_group', $group);

		return $group;
	}
}
