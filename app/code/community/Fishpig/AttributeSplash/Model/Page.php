<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Model_Page extends Fishpig_AttributeSplash_Model_Abstract
{
	/**
	 * Setup the model's resource
	 *
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('attributeSplash/page');
	}
	
	/**
	 * Retrieve the URL for the splash page
	 * If cannot find rewrite, return system URL
	 *
	 * @return string
	 */
	public function getUrl()
	{
		if ($this->hasUrl()) {
			return $this->_getData('url');
		}
		
		$uri = (Mage::getStoreConfigFlag('attributeSplash/page/include_group_url_key')
			? $this->getSplashGroup()->getUrlKey() . '/' : '')
			. $this->getUrlKey() . $this->getUrlSuffix();
		
		return $this->_getUrl($uri);
	}

	/**
	 * Determine whether the model is active
	 *
	 * @return bool
	 */
	public function isActive()
	{
		return (($page = Mage::registry('splash_page')) !== null)
			&& $page->getId() === $this->getId();
	}

	/**
	 * Retrieve the full URL of the splash image
	 *
	 * @return string
	 */
	public function getImage()
	{
		return Mage::helper('attributeSplash/image')->getImageUrl($this->getData('image'));
	}
	
	/**
	 * Retrieve the URL for the image
	 * This converts relative URL's to absolute
	 *
	 * @return string
	 */
	public function getImageUrl()
	{
		if ($this->_getData('image_url')) {
			if (strpos($this->_getData('image_url'), 'http://') === false) {
				$this->setImageUrl(Mage::getBaseUrl() . ltrim($this->_getData('image_url'), '/ '));
			}
		}
		
		return $this->_getData('image_url');
	}
	
	/**
	 * Retrieve the full URL of the splash thumbnail
	 *
	 * @return string
	 */
	public function getThumbnail()
	{
		return Mage::helper('attributeSplash/image')->getImageUrl($this->getData('thumbnail'));
	}
	
	/**
	 * Retrieve the attribute model for the page
	 *
	 * @return Mage_Eav_Model_Entity_Attribute
	 */
	public function getAttributeModel()
	{
		if (!$this->hasAttributeModel()) {
			$this->setAttributeModel(
				Mage::getModel('eav/entity_attribute')->load($this->getAttributeId())
			);
		}
		
		return $this->getData('attribute_model');
	}
	
	/**
	 * Retrieve a collection of products associated with the splash page
	 *
	 * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
	 */
	public function getProductCollection()
	{
		if (!$this->hasProductCollection()) {
			$this->setProductCollection($this->getResource()->getProductCollection($this));
		}
		
		return $this->getData('product_collection');
	}
	
	/**
	 * Retrieve the group associated with the splash page
	 * This will retrieve the most related group
	 * If there isn't a group for the same store, the admin group will be returned
	 *
	 * @return Fishpig_AttributeSplash_Model_Group|false
	 */
	public function getSplashGroup()
	{
		if (!$this->hasSplashGroup()) {
			$this->setSplashGroup($this->getResource()->getSplashGroup($this));
		}
		
		return $this->getData('splash_group');
	}
	
	public function getCategory()
	{
		if (($category = parent::getCategory()) !== false) {
			return $category;
		}
		
		return $this->getSplashGroup()->getCategory();
	}
}
