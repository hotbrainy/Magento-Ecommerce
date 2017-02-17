<?php

class Infortis_UltraMegamenu_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Get configuration
	 *
	 * @var string
	 */
	public function getCfg($optionString)
	{
		return Mage::getStoreConfig('ultramegamenu/' . $optionString);
	}

	/**
	 * Get mobile menu threshold if mobile mode enabled. Otherwise, return NULL.
	 * Important: used in other modules.
	 *
	 * @var string/NULL
	 */
	public function getMobileMenuThreshold()
	{
		if ($this->getCfg('general/mode') > 0) //Mobile mode not enabled
		{
			return NULL; //If no mobile menu, value of the threshold doesn't matter, so return NULL
		}
		else
		{
			return $this->getCfg('mobilemenu/threshold');
		}
	}

	public function getBlocksVisibilityClassOnMobile()
	{
		return 'opt-sb' . $this->getCfg('mobilemenu/show_blocks');
	}

	/**
	 * Check if current url is url for home page
	 *
	 * @return bool
	 */
	public function getIsHomePage()
	{
		return Mage::getUrl('') == Mage::getUrl('*/*/*', array('_current'=>true, '_use_rewrite'=>true));
	}

	/**
	 * @deprecated
	 * Check if current url is url for home page
	 *
	 * @return bool
	 */
	public function getIsOnHome()
	{
		$routeName = Mage::app()->getRequest()->getRouteName();
		$id = Mage::getSingleton('cms/page')->getIdentifier();
		
		if($routeName == 'cms' && $id == 'home')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * @deprecated
	 * Get icon color suffix for home link icon which is displayed in menu item
	 *
	 * @var string
	 */
	public function getHomeIconSuffix()
	{
		$packageName = Mage::getStoreConfig('design/package/name');
		$theme = Mage::helper($packageName);
		$outputSuffix = '';
		
		//Get config: w = white icon, b = black icon
		if ($this->getIsOnHome()) //If current page is homepage
		{
			$colorCurrent	= $theme->getCfgDesign('nav/mobile_opener_current_color');
			$colorHover		= $theme->getCfgDesign('nav/mobile_opener_hover_color');
			$colors = $colorCurrent . $colorHover;
		}
		else
		{
			$colorDefault	= $theme->getCfgDesign('nav/mobile_opener_color');
			$colorHover		= $theme->getCfgDesign('nav/mobile_opener_hover_color');
			$colors = $colorDefault . $colorHover;
		}

		if		($colors == 'bb') $outputSuffix = '';
		elseif	($colors == 'bw') $outputSuffix = '-bw';
		elseif	($colors == 'wb') $outputSuffix = '-wb';
		elseif	($colors == 'ww') $outputSuffix = '-w';
		
		return $outputSuffix;
	}

	/**
	 * @deprecated
	 * Get icon color suffix for home link icon which is displayed as single icon
	 *
	 * @var string
	 */
	public function getSingleHomelinkIconSuffix()
	{
		$packageName = Mage::getStoreConfig('design/package/name');
		$theme = Mage::helper($packageName);

		$suffix = ($theme->getCfgDesign('nav/home_link_icon_color') == 'b') ? '' : '-'.$theme->getCfgDesign('nav/home_link_icon_color');
		return $suffix;
	}

}
