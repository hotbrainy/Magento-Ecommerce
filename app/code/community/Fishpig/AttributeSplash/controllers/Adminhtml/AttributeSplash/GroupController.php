<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Adminhtml_AttributeSplash_GroupController extends Fishpig_AttributeSplash_Controller_Adminhtml_Abstract
{
	/**
	 * Forward the request to the dashboard
	 *
	 * @return $this
	 */
	public function indexAction()
	{
		return $this->_redirect('*/attributeSplash');
	}

	/**
	 * Add a new splash group
	 *
	 * @return $this
	 */
	public function newAction()
	{
		return $this->_forward('edit');
	}

	/**
	 * Display the add/edit form for the splash group
	 *
	 * @return void
	 */
	public function editAction()
	{
		$splash = $this->_initSplashGroup();
		$this->loadLayout();
		$this->_setActiveMenu('attributeSplash');
		
		$this->_title('FishPig');
		$this->_title('Attribute Splash');
		$this->_title($this->__('Group'));

		if ($splash) {
			$this->_title($splash->getName());
		}
		
		$this->renderLayout();
	}
	
	/**
	 * Save the posted data
	 *
	 * @return void
	 */
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost('splash')) {
			$group = Mage::getModel('attributeSplash/group')
				->setData($data)
				->setId($this->getRequest()->getParam('id'));
				
			try {
				$group->save();
				$this->_getSession()->addSuccess($this->__('Splash group was saved'));
			}
			catch (Exception $e) {
				$this->_getSession()->addError($this->__($e->getMessage()));
			}
				
			if ($group->getId() && $this->getRequest()->getParam('back', false)) {
				$this->_redirect('*/*/edit', array('id' => $group->getId()));
				return;
			}
		}
		else {
			$this->_getSession()->addError($this->__('There was no data to save.'));
		}

		$this->_redirect('*/attributeSplash');
	}

	/**
	 * Initialise the splash group model
	 *
	 * @return false|Fishpig_AttributeSplash_Model_Group
	 */
	protected function _initSplashGroup()
	{
		if (($group = Mage::registry('splash_group')) !== null) {
			return $group;
		}

		if ($id = $this->getRequest()->getParam('id')) {
			$group = Mage::getModel('attributeSplash/group')->load($id);
			
			if ($group->getId()) {
				Mage::register('splash_group', $group);
				return $group;
			}
		}
		
		return false;
	}
	
	/**
	 * Attempt to delete the group model
	 *
	 */
	public function deleteAction()
	{
		$group = Mage::getModel('attributeSplash/group')->load($this->getRequest()->getParam('id'));

		if ($group->getId() && $group->canDelete()) {
			try {
				$group->delete();
				$this->_getSession()->addSuccess($this->__('Group was deleted'));
			}
			catch (Exception $e) {
				$this->_getSession()->addError($this->__($e->getMessage()));
			}
		}

		$this->_redirect('*/attributeSplash');
	}
}
