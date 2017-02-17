<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Model_Layer extends Mage_Catalog_Model_Layer 
{
	/**
	 * Retrieve the current category
	 *
	 * @return Mage_Catalog_Model_Category
	 */
	public function getCurrentCategory()
	{	
		if (!$this->hasCurrentCategory()) {
			if ($category = $this->getSplashPage()->getCategory()) {
				$this->setData('current_category', $category);
			}
		}

		return parent::getCurrentCategory();
	}

	/**
	 * Retrieve the splash page
	 * We add an array to children_categories so that it can act as a category
	 *
	 * @return false|Fishpig_AttributeSplashPro_Model_Page
	 */
	public function getSplashPage()
	{
		if ($page = Mage::registry('splash_page')) {
			return $page;
		}
		
		return false;
	}

	/**
	 * Get the state key for caching
	 *
	 * @return string
	 */
	 public function getStateKey()
	 {
		if ($this->_stateKey === null) {
			$this->_stateKey = 'STORE_'.Mage::app()->getStore()->getId()
				. '_SPLASH_' . $this->getSplashPage()->getId()
				. '_CUSTGROUP_' . Mage::getSingleton('customer/session')->getCustomerGroupId();
			}

		return $this->_stateKey;
	}

	/**
	 * Get default tags for current layer state
	 *
	 * @param   array $additionalTags
	 * @return  array
	*/
	public function getStateTags(array $additionalTags = array())
	{
		$additionalTags = array_merge($additionalTags, array(
			Mage_Catalog_Model_Category::CACHE_TAG.$this->getSplashPage()->getId()
		));
	
		return $additionalTags;
	}

	/**
	 * Retrieve the product collection for the Splash Page
	 *
	 * @return
	 */
	 public function getProductCollection()
	 {
		 $key = 'splash_' . $this->getSplashPage()->getId();

		if (isset($this->_productCollections[$key])) {
			$collection = $this->_productCollections[$key];
		}
		else {
			$collection = $this->getSplashPage()->getProductCollection();
			$this->prepareProductCollection($collection);
			$this->_productCollections[$key] = $collection;
		}

		return $collection;
	}

	/**
	 * Stop the splash page attribute from dsplaying in the filter options
	 *
	 * @param   Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute_Collection $collection
	 * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute_Collection
     */
	protected function _prepareAttributeCollection($collection)
	{
		parent::_prepareAttributeCollection($collection);
		
		if ($splash = $this->getSplashPage()) {
			$collection->addFieldToFilter('attribute_code', array('neq' => $splash->getAttributeCode()));
		}
		
		return $collection;
	}
}
