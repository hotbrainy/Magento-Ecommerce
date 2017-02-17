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
 
class SteveB27_Publish_Model_Author extends Mage_Core_Model_Abstract
{
	const ENTITY    = 'author';
	
    const CACHE_TAG = 'publish_author';
    
    protected $_eventPrefix = 'publish_author';
    
    protected $_eventObject = 'author';
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('publish/author');
    }
    
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $now = Mage::getSingleton('core/date')->gmtDate();
        if ($this->isObjectNew()){
            $this->setCreatedAt($now);
        }
        $this->setUpdatedAt($now);
        
        return $this;
    }
    
    public function getAuthorUrl()
    {
        if ($this->getUrlKey()){
            $urlKey = '';
            if ($prefix = Mage::getStoreConfig('publish/author/url_prefix')){
                $urlKey .= $prefix.'/';
            }
            $urlKey .= $this->getUrlKey();
            if ($suffix = Mage::getStoreConfig('publish/author/url_suffix')){
                $urlKey .= '.'.$suffix;
            }
            
            return Mage::getUrl('', array('_direct'=>$urlKey));
        }
        
        return Mage::getUrl('publish/author/view', array('id'=>$this->getId()));
    }
    
    public function checkUrlKey($urlKey, $active = true){
        return $this->_getResource()->checkUrlKey($urlKey, $active);
    }

    public function getBiography()
    {
        $biography = $this->getData('biography');
        $helper = Mage::helper('cms');
        $processor = $helper->getBlockTemplateProcessor();
        $html = $processor->filter($biography);
        
        return $html;
    }
    
    public function getDefaultAttributeSetId()
    {
        return $this->getResource()->getEntityType()->getDefaultAttributeSetId();
    }
    
    public function getAttributeText($attributeCode)
    {
        $text = $this->getResource()
            ->getAttribute($attributeCode)
            ->getSource()
            ->getOptionText($this->getData($attributeCode));
        if (is_array($text)){
            return implode(', ',$text);
        }
        
        return $text;
	}

    public function getDefaultValues()
    {
        $values = array();
        $values['status'] = 1;
        $values['in_rss'] = 1;
        
        return $values;
    }
    
    public function getAuthorByEmail($email)
    {
		$authorId = $this->getResource()->getAuthorByEmail($email);
		
		return $authorId;
	}
    
    public function getProducts($sort = null,$dir = null)
    { 
	    $p = $_GET['p'];
		$order = $_GET['order'] ? $_GET['order'] : $sort;
		$dir = $_GET['dir'] ? $_GET["dir"] : $dir;
		
		if(!empty($p) && !empty($order) && !empty($dir) ){
			
		  $collection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('sku')
			->addAttributeToSelect('name')
			->addAttributeToSelect('price')
			->addAttributeToSelect('small_image')
			->addAttributeToSelect('book_series')
			->addAttributeToSelect('publish_author')
			->addAttributeToSelect('short_description')
			->addAttributeToSelect('news_from_date')
			->addAttributeToSelect('news_to_date')
			->addAttributeToSelect('special_from_date')
			->addAttributeToSelect('special_to_date')
            ->addAttributeToSelect('special_price')
			->setOrder($order, $dir)
			->setPageSize(12)
            ->setCurPage($p);
			
		}elseif(!empty($p) && empty($order) && empty($dir)){
		
		 $collection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('sku')
			->addAttributeToSelect('name')
			->addAttributeToSelect('price')
			->addAttributeToSelect('small_image')
			->addAttributeToSelect('book_series')
			->addAttributeToSelect('publish_author')
			->addAttributeToSelect('short_description')
            ->addAttributeToSelect('news_from_date')
            ->addAttributeToSelect('news_to_date')
            ->addAttributeToSelect('special_from_date')
            ->addAttributeToSelect('special_to_date')
            ->addAttributeToSelect('special_price')
			->setPageSize(12)
            ->setCurPage($p);
		
		}elseif( empty($p) && !empty($order) && !empty($dir)){
		
		 $collection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('sku')
			->addAttributeToSelect('name')
			->addAttributeToSelect('price')
			->addAttributeToSelect('small_image')
			->addAttributeToSelect('book_series')
			->addAttributeToSelect('publish_author')
			->addAttributeToSelect('short_description')
            ->addAttributeToSelect('news_from_date')
            ->addAttributeToSelect('news_to_date')
            ->addAttributeToSelect('special_from_date')
            ->addAttributeToSelect('special_to_date')
            ->addAttributeToSelect('special_price')
			->setOrder($order, $dir)
			->setPageSize(12)
            ->setCurPage(1);
			;

		}else{

		$collection = Mage::getModel('catalog/product')->getCollection()
			->addAttributeToSelect('sku')
			->addAttributeToSelect('name')
			->addAttributeToSelect('price')
			->addAttributeToSelect('small_image')
			->addAttributeToSelect('book_series')
			->addAttributeToSelect('publish_author')
			->addAttributeToSelect('short_description')
            ->addAttributeToSelect('news_from_date')
            ->addAttributeToSelect('news_to_date')
            ->addAttributeToSelect('special_from_date')
            ->addAttributeToSelect('special_to_date')
            ->addAttributeToSelect('special_price')
			->setPageSize(12)
            ->setCurPage(1);
		}

		$collection->addAttributeToFilter(array(
			array('attribute' => 'publish_author','finset' => $this->getId()),
			array('attribute' => 'publish_author', 'eq' => $this->getId()),
			)
		);
		$collection->load();
		
		Mage::getModel('review/review')->appendSummary($collection);
		
		return $collection;
	}
}
