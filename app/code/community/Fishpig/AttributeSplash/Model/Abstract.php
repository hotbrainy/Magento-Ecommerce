<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_AttributeSplash_Model_Abstract extends Mage_Core_Model_Abstract
{
	static protected $_customFields = array();
	
	/**
	 * Retrieve the name of the splash page
	 * If display name isn't set, option value label will be returned
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->getDisplayName() ? $this->getDisplayName() : $this->getFrontendLabel();
	}
	
	/**
	 * Retrieve the URL for the splash page
	 * If cannot find rewrite, return system URL
	 *
	 * @return string
	 */
	protected function _getUrl($uri)
	{
		$url = Mage::getUrl('', array(
			'_direct' => $uri,
			'_secure' 	=> false,
			'_nosid' 	=> true,
			'_store' => $this->getStoreId(),
		));
		
		if ($this->getStoreId() === 0 || Mage::getStoreConfigFlag('web/seo/use_rewrites')) {
			$url = str_replace('/' . basename($_SERVER['SCRIPT_FILENAME']), '', $url);
		}
		
		if (Mage::getStoreConfigFlag('web/url/use_store') && $this->getStoreId() === 0) {
			$url = str_replace('/admin/', '/', $url);			
		}

		return $url;
	}

	/**
	 * Retrieve the URL Base
	 * This is used for url_key field in the Admin
	 *
	 * @return string
	 */
	public function getUrlBase()
	{
		if (substr($this->_resourceName, -5) === 'group' || !Mage::getStoreConfigFlag('attributeSplash/page/include_group_url_key')) {
			return $this->_getUrl('');
		}
		
		if ($this->getSplashGroup()) {
			return $this->_getUrl(
				$this->getSplashGroup()->getUrlKey() . '/'
			);		
		}
		
		return $this->_getUrl(
			$this->getAttributeModel()->getAttributeCode() . '/'
		);
	}
	
	/**
	 * Retrieve the URL suffix from the config
	 *
	 * @return string
	 */	
	static public function getUrlSuffix()
	{
		return Mage::getStoreConfig('attributeSplash/seo/url_suffix');
	}

	/**
	 * Retrieve the description
	 * If $process is true, output will be filtered
	 *
	 * @param bool $process = true
	 * @return string
	 */
	public function getDescription($process = true)
	{
		if ($process) {
			return Mage::helper('cms')->getBlockTemplateProcessor()->filter($this->getData('description'));
		}
		
		return $this->getData('description');
	}
	
	/**
	 * Retrieve the short_description
	 *
	 * @return string
	 */
	public function getShortDescription()
	{
		return Mage::helper('cms')->getBlockTemplateProcessor()->filter($this->getData('short_description'));
	}
	
	/**
	 * Retrieve the Meta description.
	 * If empty, use the short description
	 *
	 * @return string
	 */
	public function getMetaDescription()
	{
		return $this->getData('meta_description') ? $this->getData('meta_description') : strip_tags($this->getShortDescription());
	}
	
	/**
	 * Retrieve the date/time the item was updated
	 *
	 * @param bool $includeTime = true
	 * @return string
	 */
	public function getUpdatedAt($includeTime = true)
	{
		if ($str = $this->_getData('updated_at')) {
			return $includeTime ? $str : trim(substr($str, 0, strpos($str, ' ')));
		}
		
		return '';
	}

	/**
	 * Retrieve the date/time the item was created
	 *
	 * @param bool $includeTime = true
	 * @return string
	 */
	public function getCreatedAt($includeTime = true)
	{
		if ($str = $this->_getData('created_at')) {
			return $includeTime ? $str : trim(substr($str, 0, strpos($str, ' ')));
		}
		
		return '';
	}
	
	/**
	 * Retrieve the menu node ID
	 *
	 * @return string
	 */
	public function getMenuNodeId()
	{
		return 'splash-' . substr($this->_resourceName, strpos($this->_resourceName, '/')+1) . $this->getId();
	}
	
	/**
	 * Retrieve the store ID of the splash page
	 * This isn't always the only store it's associated with
	 * but the current store ID
	 *
	 * @return int
	 */
	public function getStoreId()
	{
		if (!$this->hasStoreId() || (int)$this->_getData('store_id') === 0) {
			return (int)Mage::app()->getStore(true)->getId();
		}

		return (int)$this->_getData('store_id');
	}
	
	/**
	 * Determine whether object has a store ID of 0
	 *
	 * @return bool
	 */
	public function isGlobal()
	{
		if ($storeIds = $this->getStoreIds()) {
			foreach($storeIds as $storeId) {
				if ((int)$storeId === 0)	{
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Determine whether to include the object in the menu
	 *
	 * @return bool
	 */
	public function canIncludeInMenu()
	{
		return (int)$this->_getData('include_in_menu') !== 0;
	}
	
	/**
	 * Get the category used for layered navigation category filters
	 * 
	 * @return false|Mage_Catalog_Model_Category
	 */
	public function getCategory()
	{
		if (!$this->hasCategory()) {
			$this->setCategory(false);

			if ($this->getCategoryId()) {
				$category = Mage::getModel('catalog/category')
					->setStoreId($this->getStoreId())
					->load($this->getCategoryId());
					
				if ($category->getId()) {
					$this->setCategory($category);
				}
			}
		}
		
		return $this->_getData('category');
	}	
	
	/**
	 * Determine whether custom fields are available
	 *
	 * @return bool
	 */
	public function hasAvailableCustomFields()
	{
		return count($this->getAllAvailableCustomFields()) > 0;
	}
	
	/**
	 * Get an array of all custom fields
	 *
	 * @return array
	 */
	public function getAllAvailableCustomFields()
	{
		$type = $this instanceof Fishpig_AttributeSplash_Model_Page ? 'page' : 'group';

		if (isset(self::$_customFields[$type])) {
			return self::$_customFields[$type];
		}
		
		self::$_customFields[$type] = array();
		
		$cfString = trim(Mage::getStoreConfig('attributeSplash/custom_fields/' . $type));
		
		if (!$cfString) {
			return false;
		}

		foreach(explode("\n", $cfString) as $customField) {
			if (($customField = trim($customField)) !== '') {
				self::$_customFields[$type][$customField] = ucwords(str_replace('_', ' ', $customField));
			}
		}
		
		return self::$_customFields[$type];
	}
}
