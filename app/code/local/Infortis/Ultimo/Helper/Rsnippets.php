<?php

class Infortis_Ultimo_Helper_Rsnippets extends Mage_Core_Helper_Abstract
{
	const SCHEMA_PRODUCT			= 'itemscope itemtype="http://schema.org/Product"';
	const SCHEMA_OFFER				= 'itemprop="offers" itemscope itemtype="http://schema.org/Offer"';
	const SCHEMA_OFFER_AGGREGATE	= 'itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer"';

	/**
	 * Flag indicating that "AggregateOffer" property must be used instead of "Offer" property
	 *
	 * @var bool
	 */
	protected $_productPageAggregateOffer = false;

	/**
	 * Check if rich snippets enabled on product page
	 *
	 * @return bool
	 */
	public function isEnabledOnProductPage()
	{
		return Mage::getStoreConfig('ultimo/rsnippets/enable_product');
	}

	/**
	 * Get price rich snippets on product page
	 *
	 * @param Mage_Catalog_Model_Product
	 * @return string
	 */
	public function getPriceProperties($product)
	{
		//Get product type ID
		$productTypeId = $product->getTypeId();
		if ($productTypeId === 'grouped')
		{
			return '';
		}

		$includeTax = Mage::getStoreConfig('ultimo/rsnippets/price_incl_tax');
		$html = '<meta itemprop="priceCurrency" content="' . Mage::app()->getStore()->getCurrentCurrencyCode() . '" />';

		if ($productTypeId === 'bundle')
		{
			if ($product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED)
			{
				$minimalPrice = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), $includeTax);
				$html .= '<meta itemprop="price" content="' . $minimalPrice . '" />';
			}
			else
			{
				$pm = $product->getPriceModel(); //Mage::getModel('bundle/product_price');

				//getPricesDependingOnTax deprecated after 1.5.1.0, see Mage_Bundle_Model_Product_Price::getTotalPrices()
				//Args: product, min/max, include tax
				list($minimalPrice, $maximalPrice) = $pm->getPricesDependingOnTax($product, null, $includeTax);

				//If attribute 'price_view' true, price block is displayed with "As Low as" label.
				if ($product->getPriceView())
				{
					$html .= '<meta itemprop="price" content="' . $minimalPrice . '" />';
				}
				else //Else, display price range. Price snippets must be displayed inside "AggregateOffer" property.
				{
					$this->_productPageAggregateOffer = true;
					$html .= '<meta itemprop="lowPrice" content="' . $minimalPrice . '" />';
					$html .= '<meta itemprop="highPrice" content="' . $maximalPrice . '" />';
				}
			}
		}
		else
		{
			$html .= '<meta itemprop="price" content="' . Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), $includeTax) . '" />';
		}

		return $html;
	}

	/**
	 * Get offer property and itemscope based on '_productPageAggregateOffer'.
	 * IMPORTANT: this method must be called after 'getPriceProperties' in which '_productPageAggregateOffer' is evaluated.
	 *
	 * @return string
	 */
	public function getOfferItemscope()
	{
		if ($this->_productPageAggregateOffer)
		{
			return self::SCHEMA_OFFER_AGGREGATE;
		}
		else
		{
			return self::SCHEMA_OFFER;
		}
	}

	/**
	 * Get product itemscope
	 *
	 * @return string
	 */
	public function getProductItemscope()
	{
		return self::SCHEMA_PRODUCT;
	}
}