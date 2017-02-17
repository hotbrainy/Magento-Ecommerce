<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Model_Group extends Fishpig_AttributeSplash_Model_Abstract
{
	/**
	 * Setup the model's resource
	 *
	 * @return void
	 */
	public function _construct()
	{
		$this->_init('attributeSplash/group');
	}
	
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
	 * Determine whether the model is active
	 *
	 * @return bool
	 */
	public function isActive()
	{
		return (($group = Mage::registry('splash_group')) !== null)
			&& $group->getId() === $this->getId();
	}
		
	/**
	 * Retrieve the URL for the splash group
	 * If cannot find rewrite, return system URL
	 *
	 * @return string
	 */
	public function getUrl()
	{
		if (!$this->hasUrl()) {
			$this->setUrl(
				$this->_getUrl($this->getUrlKey() . $this->getUrlSuffix())
			);
		}
		
		return $this->_getData('url');
	}
	
	/**
	 * Retrieve the attribute model for the page
	 *
	 * @return Mage_Eav_Model_Entity_Attribute
	 */
	public function getAttributeModel()
	{
		if (!$this->hasAttributeModel()) {
			$this->setAttributeModel($this->getResource()->getAttributeModel($this));
		}
		
		return $this->getData('attribute_model');
	}
	
	/**
	 * Retrieve a collection of products associated with the splash page
	 *
	 * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
	 */
	public function getSplashPages()
	{
		return $this->getResource()->getSplashPages($this);
	}
	
	/**
	 * Determine whether this group has any splash pages
	 *
	 * @return bool
	 */
	public function hasSplashPages()
	{
		return count($this->getSplashPages()) > 0;
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
	 * Determine whether it's possible to delete the group
	 *
	 * @return bool
	 */
	public function canDelete()
	{
		return $this->getResource()->canDelete($this);
	}
}
