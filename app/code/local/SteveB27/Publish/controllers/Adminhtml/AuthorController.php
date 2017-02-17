<?php
/**
 * Custom Publisher Models
 * 
 * Add custom model types, such as author, which can be used as a product
 * attribute while proviting additional details.
 * 
 * @license 	http://opensource.org/licenses/gpl-license.php GNU General Public License, Version 3
 * @copyright	Steven Brown March 12, 2016
 * @author		Steven Brown <steveb.27@outlook.com>
 */

class SteveB27_Publish_Adminhtml_AuthorController extends Mage_Adminhtml_Controller_Action
{
    protected function _construct(){
        $this->setUsedModuleName('SteveB27_Publish');
    }
    
    protected function _initAuthor(){
        $this->_title($this->__('Publish'))
             ->_title($this->__('Manage Authors'));

        $articleId  = (int) $this->getRequest()->getParam('id');
        $article    = Mage::getModel('publish/author')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if ($articleId) {
            $article->load($articleId);
        }
        Mage::register('current_author', $article);
        return $article;
    }
    
    public function indexAction(){
        $this->_title($this->__('Publish'))
             ->_title($this->__('Manage Authors'));
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function newAction(){
        $this->_forward('edit');
    }
    
    public function editAction(){
        $articleId  = (int) $this->getRequest()->getParam('id');

        $article = $this->_initAuthor();
        if ($articleId && !$article->getId()) {
            $this->_getSession()->addError(Mage::helper('publish')->__('This author no longer exists.'));
            $this->_redirect('*/*/');
            return;
        }
        if ($data = Mage::getSingleton('adminhtml/session')->getAuthorData(true)){
            $article->setData($data);
        }
        $this->_title($article->getName());
        Mage::dispatchEvent('publish_author_edit_action', array('author' => $article));
        $this->loadLayout();
        if ($article->getId()){
            if (!Mage::app()->isSingleStoreMode() && ($switchBlock = $this->getLayout()->getBlock('store_switcher'))) {
                $switchBlock->setDefaultStoreName(Mage::helper('publish')->__('Default Values'))
                    ->setSwitchUrl($this->getUrl('*/*/*', array('_current'=>true, 'active_tab'=>null, 'tab' => null, 'store'=>null)));
            }
			else {
				$this->getLayout()->getBlock('left')->unsetChild('store_switcher');
			}
        }
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->renderLayout();
    }
    
    public function saveAction(){
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $articleId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);
        $data = $this->getRequest()->getPost();
        if ($data) {
            $article    = $this->_initAuthor();
            $articleData = $this->getRequest()->getPost('author', array());
            $article->addData($articleData);
            $article->setAttributeSetId($article->getDefaultAttributeSetId());
            if ($useDefaults = $this->getRequest()->getPost('use_default')) {
                foreach ($useDefaults as $attributeCode) {
                    $article->setData($attributeCode, false);
                }
            }
            try {
                $article->save();
                $articleId = $article->getId();
                $this->_getSession()->addSuccess(Mage::helper('publish')->__('Author was saved'));
            }
            catch (Mage_Core_Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage())
                    ->setAuthorData($articleData);
                $redirectBack = true;
            }
            catch (Exception $e){
                Mage::logException($e);
                $this->_getSession()->addError(Mage::helper('publish')->__('Error saving author'))
                    ->setAuthorData($articleData);
                $redirectBack = true;
            }
        }
        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $articleId,
                '_current'=>true
            ));
        }
        else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }
    public function deleteAction(){
        if ($id = $this->getRequest()->getParam('id')) {
            $article = Mage::getModel('publish/author')->load($id);
            try {
                $article->delete();
                $this->_getSession()->addSuccess(Mage::helper('publish')->__('The author has been deleted.'));
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->getResponse()->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
    }
    public function massDeleteAction() {
        $articleIds = $this->getRequest()->getParam('author');
        if (!is_array($articleIds)) {
            $this->_getSession()->addError($this->__('Please select authors.'));
        }
        else {
            try {
                foreach ($articleIds as $articleId) {
                    $article = Mage::getSingleton('publish/author')->load($articleId);
                    Mage::dispatchEvent('publish_author_controller_author_delete', array('author' => $article));
                    $article->delete();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('publish')->__('Total of %d record(s) have been deleted.', count($articleIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    public function massStatusAction(){
        $articleIds = $this->getRequest()->getParam('author');
        if(!is_array($articleIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('publish')->__('Please select authors.'));
        }
        else {
            try {
                foreach ($articleIds as $articleId) {
                $article = Mage::getSingleton('publish/author')->load($articleId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess($this->__('Total of %d authors were successfully updated.', count($articleIds)));
            }
            catch (Mage_Core_Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('publish')->__('There was an error updating authors.'));
                Mage::logException($e);
            }
        }
        $this->_redirect('*/*/index');
    }
    public function gridAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
     protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('publish/author');
    }

    public function exportCsvAction(){
        $fileName   = 'articles.csv';
        $content    = $this->getLayout()->createBlock('publish/adminhtml_author_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    public function exportExcelAction(){
        $fileName   = 'article.xls';
        $content    = $this->getLayout()->createBlock('publish/adminhtml_author_grid')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    public function exportXmlAction(){
        $fileName   = 'authors.xml';
        $content    = $this->getLayout()->createBlock('publish/adminhtml_author_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    public function wysiwygAction() {
        $elementId = $this->getRequest()->getParam('element_id', md5(microtime()));
        $storeId = $this->getRequest()->getParam('store_id', 0);
        $storeMediaUrl = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA);

        $content = $this->getLayout()->createBlock('publish/adminhtml_author_helper_form_wysiwyg_content', '', array(
            'editor_element_id' => $elementId,
            'store_id'          => $storeId,
            'store_media_url'   => $storeMediaUrl,
        ));
        $this->getResponse()->setBody($content->toHtml());
    }
}