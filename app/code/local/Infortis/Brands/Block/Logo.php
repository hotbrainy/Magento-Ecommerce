<?php
/**
 * Product brand
 */

class Infortis_Brands_Block_Logo extends Infortis_Brands_Block_Abstract
{
	/**
	 * Brand name of the current product
	 *
	 * @var string
	 */
	protected $_currentBrand;

	/**
	 * Resource initialization
	 */
	protected function _construct()
	{
		parent::_construct();

		$this->addData(array(
			'cache_lifetime'    => 31536000,
			'cache_tags'        => array(Mage_Cms_Model_Block::CACHE_TAG),
		));
	}

	/**
	 * Get cache key informative items
	 *
	 * @return array
	 */
	public function getCacheKeyInfo()
	{
		return array(
			'BRANDS_LOGO',
			Mage::app()->getStore()->getId(),
			$this->getTemplateFile(),
			'template' => $this->getTemplate(),
			(int)Mage::app()->getStore()->isCurrentlySecure(),

			$this->getCurrentBrand(),
		);
	}

	/**
	 * Get current product's brand
	 *
	 * @return string
	 */
	public function getCurrentBrand()
	{
		if (NULL === $this->_currentBrand)
		{
			$this->_currentBrand = $this->getBrand(Mage::registry('current_product'));
		}
		return $this->_currentBrand;
	}

	/**
	 * Deprecated
	 * Returns current product
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	public function getCurrentProductObject()
	{
		return Mage::registry('current_product');
	}
}
