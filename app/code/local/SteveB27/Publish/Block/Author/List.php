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

class SteveB27_Publish_Block_Author_List extends Mage_Core_Block_Template 
{
     public function __construct() {
        parent::__construct();
        
        $authors = Mage::getModel('publish/author')->getCollection();
        //   ->setStoreId(Mage::app()->getStore()->getId())
        $authors->addAttributeToSelect('*');
        $authors->addAttributeToFilter('status', 1);	// TODO: Fix filter bug
        $authors->setOrder('name', 'asc');
         if($author = $this->getRequest()->getParam("author")){
             foreach(explode(" ",$author) as $authorPart){
                 $authors->addFieldToFilter("name",array("like"=>"%".trim($authorPart)."%"));
             }
         }
        $this->setAuthorCollection($authors);
    }
    
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('page/html_pager', 'publish.author.html.pager');
        $pager->setAvailableLimit(array(24=>24));
        $pager->setCollection($this->getAuthorCollection());
        $this->setChild('pager', $pager);
        $this->getAuthorCollection()->load();
        
        return $this;
    }

    public function getSearchTerm(){
        $author = $this->getRequest()->getParam("author");

        return $author ? $author : "";
    }
    
    public function getPagerHtml(){
        return $this->getChildHtml('pager');
    }

    public function getAuthorSearchUrl(){
        return $this->getUrl("*/*/*");
    }

    public function getAuthorPaginationUrl(){
        $currentUrl = strtok(Mage::helper('core/url')->getCurrentUrl(),"?");
        $params = $this->getRequest()->getParam("author") ? "?author=".$this->getRequest()->getParam("author")."&" : "?";

        return $currentUrl.$params;
    }
}