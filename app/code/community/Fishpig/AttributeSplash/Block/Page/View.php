<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Page_View extends Mage_Core_Block_Template
{
	/**
	 * Adds the META information to the resulting page
	 */
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		
		if ($layoutCode = Mage::getStoreConfig('attributeSplash/frontend/template')) {
			if ($templateData = Mage::getSingleton('page/config')->getPageLayout($layoutCode)) {
				if (isset($templateData['template'])) {
					$this->getLayout()->getBlock('root')->setTemplate($templateData['template']);
				}		
			}
		}

        return $this;
    }
    
    /**
     * Retrieves the current Splash model
     *
     * @return Fishpig_AttributeSplash_Model_Splash|null
     */
	public function getSplashPage()
	{
		if (!$this->hasSplashPage()) {
			if ($this->hasSplashPageId()) {
				$this->setSplashPage(Mage::getModel('attributeSplash/splash')->load($this->getSplashPageId()));
			}
			else {
				$this->setSplashPage(Mage::registry('splash_page'));
			}
		}
		
		return $this->getData('splash_page');
	}

	/**
	 * Check if category display mode is "Products Only"
	 *
	 * @return bool
	*/
	public function isProductMode()
	{
		return $this->getSplashPage()->getDisplayMode()==Mage_Catalog_Model_Category::DM_PRODUCT;
	}
	
	/**
	 * Check if category display mode is "Static Block and Products"
	 *
	 * @return bool
	*/
	public function isMixedMode()
	{
		return $this->getSplashPage()->getDisplayMode()==Mage_Catalog_Model_Category::DM_MIXED;
	}

	/**
	 * Determine whether it is content mode (Static Block)
	 *
	 * @return bool
	 */
	public function isContentMode()
	{
		return $this->getSplashPage()->getDisplayMode()==Mage_Catalog_Model_Category::DM_PAGE;
	}
	
	/**
	 * Retrieves and renders the product list block
	 *
	 * @return string
	 */
	public function getProductListHtml()
	{
		return $this->getProductListBlock()->toHtml();
	}
	
	/**
	 * Retrieve the product list block
	 *
	 * @return Mage_Catalog_Block_Product_List
	 */
	public function getProductListBlock()
	{
		if ($block = $this->getChild('product_list')) {
			if (!$block->hasColumnCount()) {
				$block->setColumnCount($this->getSplashPageProductsPerRow());
			}

			return $block;
		}
		
		return false;
	}
	
	/**
	 * Retrieve the number of products per row
	 *
	 * @return int
	 */
	public function getSplashPageProductsPerRow()
	{
		return Mage::getStoreConfig('attributeSplash/page/column_count');
	}
	
	/**
	 * Retrieves the HTML for the CMS block
	 *
	 * @return string
	 */
	public function getCmsBlockHtml()
	{
		if (!$this->getData('cms_block_html')) {
			$html = $this->getLayout()->createBlock('cms/block')
				->setBlockId($this->getSplashPage()->getCmsBlock())->toHtml();

			$this->setData('cms_block_html', $html);
		}
		
		return $this->getData('cms_block_html');
	}
}
