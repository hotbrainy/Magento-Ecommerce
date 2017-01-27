<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
class Fishpig_AttributeSplash_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Retrieve a splash page for the product / attribute code combination
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @param $attributeCode
	 * @return Fishpig_AttributeSplash_Model_Splash|null
	 */
	public function getProductSplashPage(Mage_Catalog_Model_Product $product, $attributeCode)
	{
		$key = $attributeCode . '_splash_page';
		
		if (!$product->hasData($key)) {
			$product->setData($key, false);

			$collection = Mage::getResourceModel('attributeSplash/page_collection')
				->addStoreFilter(Mage::app()->getStore())
				->addAttributeCodeFilter($attributeCode)
				->addProductFilter($product)
				->setPageSize(1)
				->setCurPage(1)
				->load();

			if (count($collection) > 0) {
				$page = $collection->getFirstItem();
				
				if ($page->getId()) {
					$product->setData($key, $page);
				}
			}
		}
		
		return $product->getData($key);
	}
	
	/**
	 * Log an error message
	 *
	 * @param string $msg
	 * @return Fishpig_AttributeSplash_Helper_Data
	 */
	public function log($msg)
	{
		Mage::log($msg, false, 'attributeSplash.log', true);

		return $this;
	}
	
	/**
	 * Get the URL suffix
	 *
	 * @return string
	 */
	public function getUrlSuffix()
	{
		return Mage::getStoreConfig('attributeSplash/seo/url_suffix');	
	}
	
	/**
	 * Determine whether the group URL key is used in the page URL
	 *
	 * @return bool
	 */
	public function includeGroupUrlKeyInPageUrl()
	{
		return Mage::getStoreConfigFlag('attributeSplash/page/include_group_url_key');
	}
	
	/**
	 * Determiner whether Fishpig_FSeo is installed
	 *
	 * @return bool
	 */
	public function isFishPigSeoInstalledAndActive()
	{
		return 'true' === (string)Mage::getConfig()->getNode('modules/Fishpig_FSeo/active')
			&& Mage::helper('fseo/layer')->isEntityTypeEnabled('attributeSplash_page');
	}
	
	/**
	 * Disable the MageWorx_SeoSuite rewrites for the layered navigation
	 *
	 * @return $this
	 */
	public function clearLayerRewrites()
	{
		Mage::getConfig()->setNode('modules/MageWorx_SeoSuite/active', 'false', true);
		Mage::getConfig()->setNode('global/models/catalog/rewrite/layer_filter_item', null, true);
		Mage::getConfig()->setNode('global/models/catalog/rewrite/layer_filter_attribute', null, true);
		Mage::getConfig()->setNode('global/models/catalog/rewrite/layer_filter_category', null, true);			
		Mage::getConfig()->setNode('global/models/catalog_resource/rewrite/layer_filter_item', null, true);
		Mage::getConfig()->setNode('global/models/catalog_resource_eav_mysql4/rewrite/layer_filter_item', null, true);
		Mage::getConfig()->setNode('global/blocks/catalog/rewrite/product_list_toolbar', null, true);
		Mage::getConfig()->setNode('global/blocks/catalog/rewrite/layer_filter_attribute', null, true);
			
		if ($this->isFishPigSeoInstalledAndActive()) {
			Mage::helper('fseo/layer')->applyLayerRewrites();
		}
		
		return $this;
	}
}
