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
 
class SteveB27_Publish_AuthorController extends Mage_Core_Controller_Front_Action
{
    public function indexAction(){
         $this->loadLayout();
         $this->_initLayoutMessages('catalog/session');
         $this->_initLayoutMessages('customer/session');
         $this->_initLayoutMessages('checkout/session');
         if (Mage::helper('publish/author')->getUseBreadcrumbs()){
             if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')){
                 $breadcrumbBlock->addCrumb('home', array(
                            'label'    => Mage::helper('publish')->__('Home'),
                            'link'     => Mage::getUrl(),
                        )
                 );
                 $breadcrumbBlock->addCrumb('author', array(
                            'label'    => Mage::helper('publish')->__('Authors'),
                            'link'    => '',
                    )
                 );
             }
         }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle(Mage::getStoreConfig('publish/author/meta_title'));
            $headBlock->setKeywords(Mage::getStoreConfig('publish/author/meta_keywords'));
            $headBlock->setDescription(Mage::getStoreConfig('publish/author/meta_description'));
        }
        $this->renderLayout();
    }
    
    protected function _initAuthor(){
        $articleId   = $this->getRequest()->getParam('id', 0);
        $article     = Mage::getModel('publish/author')
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->load($articleId);
        if (!$article->getId()){
            return false;
        }
        elseif (!$article->getStatus()){
            return false;
        }
        return $article;
    }
    
    public function viewAction(){
        $article = $this->_initAuthor();
        if (!$article) {
            $this->_forward('no-route');
            return;
        }
        Mage::register('current_author', $article);
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('checkout/session');
        if ($root = $this->getLayout()->getBlock('root')) {
            $root->addBodyClass('publish-author publish-author' . $article->getId());
        }
        if (Mage::helper('publish/author')->getUseBreadcrumbs()){
            if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')){
                $breadcrumbBlock->addCrumb('home', array(
                            'label'    => Mage::helper('publish')->__('Home'),
                            'link'     => Mage::getUrl(),
                        )
                );
                $breadcrumbBlock->addCrumb('author', array(
                            'label'    => Mage::helper('publish')->__('Authors'),
                            'link'    => Mage::helper('publish/author')->getAuthorsUrl(),
                    )
                );
                $breadcrumbBlock->addCrumb('author', array(
                            'label'    => $article->getTitle(),
                            'link'    => '',
                    )
                );
            }
        }
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            if ($article->getMetaTitle()){
                $headBlock->setTitle($article->getMetaTitle());
            }
            else{
                $headBlock->setTitle($article->getTitle());
            }
            $headBlock->setKeywords($article->getMetaKeywords());
            $headBlock->setDescription($article->getMetaDescription());
        }
        $this->renderLayout();
    }
    
    public function rssAction(){
        if (Mage::helper('publish/author')->isRssEnabled()) {
            $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
            $this->loadLayout(false);
            $this->renderLayout();
        }
        else {
            $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
            $this->getResponse()->setHeader('Status','404 File not found');
            $this->_forward('nofeed','index','rss');
        }
    }
}