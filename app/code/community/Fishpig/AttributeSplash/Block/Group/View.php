<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Group_View extends Mage_Core_Block_Template
{
	/**
	 * Splash page collection
	 *
	 * @var Fishpig_AttributeSplash_Model_Mysql4_Page_Collection
	 */
	protected $_splashPages = null;
	
	/**
	 * Retrieve the splash group
	 *
	 * @return Fishpig_AttributeSplash_Model_Group
	 */
	public function getSplashGroup()
	{
		if (!$this->hasSplashGroup()) {
			return Mage::registry('splash_group');
		}
		
		return $this->_getData('splash_group');
	}
	
	/**
	 * Retrieve the splash page collection
	 *
	 * @return Fishpig_AttributeSplash_Model_Mysql4_Page_Collection
	 */
	public function getSplashPages()
	{
		if (is_null($this->_splashPages)) {
			$this->_splashPages = $this->getSplashGroup()
				->getSplashPages()
				->addOrderBySortOrder();
		}
		
		return $this->_splashPages;
	}
	
	/**
	 * Check if category display mode is "Products Only"
	 *
	 * @return bool
	*/
	public function isProductMode()
	{
		return $this->getSplashGroup()->getDisplayMode()==Mage_Catalog_Model_Category::DM_PRODUCT;
	}
	
	/**
	 * Check if category display mode is "Static Block and Products"
	 *
	 * @return bool
	*/
	public function isMixedMode()
	{
		return $this->getSplashGroup()->getDisplayMode()==Mage_Catalog_Model_Category::DM_MIXED;
	}

	/**
	 * Determine whether it is content mode (Static Block)
	 *
	 * @return bool
	 */
	public function isContentMode()
	{
		return $this->getSplashGroup()->getDisplayMode()==Mage_Catalog_Model_Category::DM_PAGE;
	}

	/**
	 * Retrieves the HTML for the CMS block
	 *
	 * @return string
	 */
	public function getCmsBlockHtml()
	{
		if (!$this->_getData('cms_block_html')) {
			$html = $this->getLayout()->createBlock('cms/block')
				->setBlockId($this->getSplashGroup()->getCmsBlock())->toHtml();

			$this->setCmsBlockHtml($html);
		}
		
		return $this->_getData('cms_block_html');
	}
	
	/**
	 * Retrieve the amount of columns for grid view
	 *
	 * @return int
	 */
	public function getColumnCount()
	{
		return $this->hasColumnCount() 
			? $this->_getData('column_count') 
			: Mage::getStoreConfig('attributeSplash/group/column_count');
	}
	
	/**
	 * Get the Thumbnail URL for $page
	 *
	 * @return int
	 */
	public function getThumbnailUrl($page)
	{
		return $this->helper('attributeSplash/image')->init($page, 'thumbnail')
			->keepFrame($page->thumbnailShouldKeepFrame())
			->resize($page->getThumbnailWidth(), $page->getThumbnailHeight());
	}
	
	/**
	 * If a child block with an alias of 'pager' is set then setup the pager
	 *
	 * @return $this
	 **/
	protected function _beforeToHtml()
	{
		if ($pagerBlock = $this->getChild('pager')) {
			$this->setPagerBlock($pagerBlock);
			
			$this->getPagerBlock()->setCollection($this->getSplashPages());
		}

		return parent::_beforeToHtml();
	}
	
	/**
	 * Get the pager HTML if a pager is present
	 *
	 * @return string|null
	 **/
	public function getPagerHtml()
	{
		return $this->hasPagerBlock() ? $this->getPagerBlock()->toHtml() : null;
	}
}
