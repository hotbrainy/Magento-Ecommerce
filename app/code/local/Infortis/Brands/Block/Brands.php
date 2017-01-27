<?php
/**
 * Brand slider
 */

class Infortis_Brands_Block_Brands extends Infortis_Brands_Block_List
{
	/**
	 * Get cache key informative items
	 *
	 * @return array
	 */
	public function getCacheKeyInfo()
	{
		if (NULL === $this->_cacheKeyArray)
		{
			$this->_cacheKeyArray = array(
				'BRANDS_SLIDER',
				Mage::app()->getStore()->getId(),
				$this->getTemplateFile(),
				'template' => $this->getTemplate(),
				(int)Mage::app()->getStore()->isCurrentlySecure(),

				$this->getBrandAttributeId(),
				$this->_getFinalCollectionCacheKey(),

				$this->getBlockName(),
				$this->getShowItems(),
				$this->getIsResponsive(),
				$this->getBreakpoints(),
				$this->getTimeout(),
				$this->getLoop(),
			);
		}

		return $this->_cacheKeyArray;
	}
}
