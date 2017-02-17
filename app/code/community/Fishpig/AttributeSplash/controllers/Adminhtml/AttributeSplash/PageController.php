<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Adminhtml_AttributeSplash_PageController extends Fishpig_AttributeSplash_Controller_Adminhtml_Abstract
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
	 * Add a new splash page
	 *
	 * @return $this
	 */
	public function newAction()
	{
		return $this->_forward('edit');
	}
		
	/**
	 * Display the add/edit form for the splash page
	 *
	 */
	public function editAction()
	{
		$splash = $this->_initSplashPage();
		
		$this->loadLayout();
		$this->_setActiveMenu('attributeSplash');
		
		$this->_title('FishPig');
		$this->_title('Attribute Splash');
		$this->_title($this->__('Page'));
		
		if ($splash) {
			$this->_title($splash->getName());
		}
		
		$this->renderLayout();
	}
	
	protected function _initValidAttributeSessionMessage()
	{
		if (($page = $this->_initSplashPage()) !== false) {
			if ($page->getAttributeModel() && !$page->getAttributeModel()->getData('is_filterable')) {
				$page->getAttributeModel()->setIsFilterable(1)->save();
			}
		}
		
		return $this;
	}
	
	/**
	 * Save the posted data
	 *
	 */
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost('splash')) {
			$page = Mage::getModel('attributeSplash/page')
				->setData($data)
				->setId($this->getRequest()->getParam('id'));
				
			try {
				$this->_handleImageUpload($page, 'image');
				$this->_handleImageUpload($page, 'thumbnail');
				
				$page->save();
				$this->_getSession()->addSuccess($this->__('Splash page was saved'));
				
				$this->_initValidAttributeSessionMessage();
			}
			catch (Exception $e) {
				$this->_getSession()->addError($this->__($e->getMessage()));
			}
				
			if ($page->getId() && $this->getRequest()->getParam('back', false)) {
				return $this->_redirect('*/*/edit', array('id' => $page->getId()));
			}
			
			return $this->_redirect('*/attributeSplash');
		}

		$this->_getSession()->addError($this->__('There was no data to save.'));

		return $this->_redirect('*/attributeSplash');
	}
	
	public function optionsAction()
	{
		try {
			$attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $this->getRequest()->getParam('attribute', 0));
			
			if (!$attribute->getId()) {
				throw new Exception(Mage::helper('adminhtml')->__('This attribute no longer exists.'));
			}
			

			$this->getResponse()
				->setBody(
					Mage::helper('core')->jsonEncode(array('options' => $attribute->getSource()->getAllOptions(false)))
				);
		}
		catch (Exception $e) {
			$this->getResponse()
				->setBody(
					Mage::helper('core')->jsonEncode(array('error' => $e->getMessage()))
				);
		}
	}

	/**
	 * Delete a splash page
	 *
	 */
	public function deleteAction()
	{
		if ($pageId = $this->getRequest()->getParam('id')) {
			$splashPage = Mage::getModel('attributeSplash/page')->load($pageId);
			
			if ($splashPage->getId()) {
				try {
					$splashPage->delete();
					$this->_getSession()->addSuccess($this->__('The Splash Page was deleted.'));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}
		
		$this->_redirect('*/attributeSplash');
	}
	
	public function massDeleteAction()
	{
		$pageIds = $this->getRequest()->getParam('page');

		if (!is_array($pageIds)) {
			$this->_getSession()->addError($this->__('Please select page(s).'));
		}
		else {
			if (!empty($pageIds)) {
				try {
					foreach ($pageIds as $pageId) {
						$page = Mage::getSingleton('attributeSplash/page')->load($pageId);
						
						if ($page->getId()) {
							Mage::dispatchEvent('attributeSplash_controller_page_delete', array('splash_page' => $page, 'page' => $page));
	
							$page->delete();
						}
					}
					
					$this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', count($pageIds)));
				}
				catch (Exception $e) {
					$this->_getSession()->addError($e->getMessage());
				}
			}
		}
		
		$this->_redirect('*/attributeSplash');
	}
	
	/**
	 * Initialise the splash page model
	 *
	 * @return false|Fishpig_AttributeSplash_Model_Page
	 */
	protected function _initSplashPage()
	{
		if (($page = Mage::registry('splash_page')) !== null) {
			return $page;
		}

		if ($id = $this->getRequest()->getParam('id')) {
			$page = Mage::getModel('attributeSplash/page')->load($id);
			
			if ($page->getId()) {
				Mage::register('splash_page', $page);
				return $page;
			}
		}
		
		return false;
	}

	protected function _handleImageUpload(Fishpig_AttributeSplash_Model_Page $page, $field)
	{
		$data = $page->getData($field);

		if (isset($data['value'])) {
			$page->setData($field, $data['value']);
		}

		if (isset($data['delete']) && $data['delete'] == '1') {
			$page->setData($field, '');
		}

		if ($filename = Mage::helper('attributeSplash/image')->uploadImage($field)) {
			$page->setData($field, $filename);
		}
	}
}
