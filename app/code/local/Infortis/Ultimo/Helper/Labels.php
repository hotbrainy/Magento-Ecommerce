<?php

class Infortis_Ultimo_Helper_Labels extends Mage_Core_Helper_Abstract
{
	/**
	 * Get product labels (HTML)
	 *
	 * @return string
	 */
	public function getLabels($product,$isPdp = false)
	{
		$html = '';

		$isNew = false;
		if (Mage::getStoreConfig('ultimo/product_labels/new'))
		{	
			$isNew = $this->isNew($product);
		}
		
		$isSale = false;
		if (Mage::getStoreConfig('ultimo/product_labels/sale'))
		{
			$isSale = $this->isOnSale($product);
		}

        if($isPdp){
            if ($isNew == true && $isSale == true){
                $html .= '<span class="sticker-wrapper top-right"><span class="sticker sale"></span><span class="sticker new"></span></span>';
            }else{
                if ($isNew == true){
                    $html .= '<span class="sticker-wrapper top-right"><span class="sticker new"></span></span>';
                }
                if ($isSale == true){
                    $html .= '<span class="sticker-wrapper top-right"><span class="sticker sale"></span></span>';
                }
            }
        }else{
            if ($isNew == true){
                $html .= '<span class="sticker-wrapper top-left"><span class="sticker new"></span></span>';
            }
            if ($isSale == true){
                $html .= '<span class="sticker-wrapper top-right"><span class="sticker sale"></span></span>';
            }
        }

		return $html;
	}
	
	/**
	 * Check if "new" label is enabled and if product is marked as "new"
	 *
	 * @return  bool
	 */
	public function isNew($product)
	{
		return $this->_nowIsBetween($product->getData('news_from_date'), $product->getData('news_to_date'));
	}
	
	/**
	 * Check if "sale" label is enabled and if product has special price
	 *
	 * @return  bool
	 */
	public function isOnSale($product)
	{
		$specialPrice = number_format($product->getFinalPrice(), 2);
		$regularPrice = number_format($product->getPrice(), 2);
		
		if ($specialPrice != $regularPrice)
			return $this->_nowIsBetween($product->getData('special_from_date'), $product->getData('special_to_date'));
		else
			return false;
	}
	
	protected function _nowIsBetween($fromDate, $toDate)
	{
		if ($fromDate)
		{
			$fromDate = strtotime($fromDate);
			$toDate = strtotime($toDate);
			$now = strtotime(Mage::app()->getLocale()->date()->setTime('00:00:00')->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
			
			if ($toDate)
			{
				if ($fromDate <= $now && $now <= $toDate)
					return true;
			}
			else
			{
				if ($fromDate <= $now)
					return true;
			}
		}
		
		return false;
	}
}
