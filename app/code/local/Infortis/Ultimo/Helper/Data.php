<?php

class Infortis_Ultimo_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Patterns
	 *
	 * @var array
	 */
	protected $_texPath;
	
	/**
	 * Background images
	 *
	 * @var array
	 */
	protected $_bgImagesPath;
	
	/**
	 * Prepare paths
	 */
	public function __construct()
	{
		//Create paths
		$this->_texPath = 'wysiwyg/infortis/ultimo/_patterns/default/';
		$this->_bgImagesPath = 'wysiwyg/infortis/ultimo/_backgrounds/';
	}
	
	

	// Get theme config (group) /////////////////////////////////////////////////////////////////
	
	/**
	 * Get selected group from the main section (main settings) of the configuration array
	 *
	 * @return array
	 */
	public function getCfgGroup($group, $storeId = NULL)
	{
		if ($storeId)
			return Mage::getStoreConfig('ultimo/' . $group, $storeId);
		else
			return Mage::getStoreConfig('ultimo/' . $group);
	}
	
	/**
	 * Get theme's design section from the configuration array
	 *
	 * @return array
	 */
	public function getCfgSectionDesign($storeId = NULL)
	{
		if ($storeId)
			return Mage::getStoreConfig('ultimo_design', $storeId);
		else
			return Mage::getStoreConfig('ultimo_design');
	}
	
	/**
	 * Deprecated: old method - for backward compatibility
	 */
	public function getDesignCfgSection($storeId = NULL)
	{
		return $this->getCfgSectionDesign($storeId);
	}
	
	
	
	// Get theme config /////////////////////////////////////////////////////////////////
	
	/**
	 * Get theme's main settings (single option)
	 *
	 * @return string
	 */
	public function getCfg($optionString)
	{
		return Mage::getStoreConfig('ultimo/' . $optionString);
	}
	
	/**
	 * Get theme's design settings (single option)
	 *
	 * @return string
	 */
	public function getCfgDesign($optionString, $storeCode = NULL)
	{
		return Mage::getStoreConfig('ultimo_design/' . $optionString, $storeCode);
	}
	
	/**
	 * Get theme's layout settings (single option)
	 *
	 * @return string
	 */
	public function getCfgLayout($optionString, $storeCode = NULL)
	{
		return Mage::getStoreConfig('ultimo_layout/' . $optionString, $storeCode);
	}

	/**
	 * Deprecated: old methods - for backward compatibility
	 */
	public function getDesignCfg($optionString)
	{
		return $this->getCfgDesign($optionString);
	}
	public function getLayoutCfg($optionString, $storeCode = NULL)
	{
		return $this->getCfgLayout($optionString, $storeCode);
	}



	// Get selected settings /////////////////////////////////////////////////////////////////

	/**
	 * Get maximum page width from the config
	 *
	 * @return int
	 */
	public function getMaxWidth($storeCode = NULL)
	{
		$w = $this->getCfgLayout('responsive/max_width', $storeCode);
		if ($w == 'custom')
		{
			return intval($this->getCfgLayout('responsive/max_width_custom', $storeCode));
		}
		else
		{
			return intval($w);
		}
	}
	
	/**
	 * Get custom page width from the config.
	 * Custom width is returned only if predefined width was not selected.
	 *
	 * @return int. Return 0 if predefined width was selected.
	 */
	public function getCustomWidth($storeCode = NULL)
	{
		$w = $this->getCfgLayout('responsive/max_width', $storeCode);
		if ($w == 'custom')
		{
			return intval($this->getCfgLayout('responsive/max_width_custom', $storeCode));
		}
		else
		{
			return 0;
		}
	}



	// Background images and textures /////////////////////////////////////////////////////////////////

	/**
	 * Get background images directory path
	 *
	 * @return string
	 */
	public function getBgImagesPath()
	{
		return $this->_bgImagesPath;
	}
	
	/**
	 * Get textures/patterns directory path
	 *
	 * @return string
	 */
	public function getTexPath()
	{
		return $this->_texPath;
	}



	// Other /////////////////////////////////////////////////////////////////

	/**
	 * Get alternative image HTML of the given product
	 *
	 * @param Mage_Catalog_Model_Product	$product		Product
	 * @param int							$w				Image width
	 * @param int							$h				Image height
	 * @param string						$imgVersion		Image version: image, small_image, thumbnail
	 * @return string
	 */
	public function getAltImgHtml($product, $w, $h, $imgVersion='small_image')
	{
		$column = $this->getCfg('category/alt_image_column');
		$value = $this->getCfg('category/alt_image_column_value');
		$product->load('media_gallery');
		if ($gal = $product->getMediaGalleryImages())
		{
			if ($altImg = $gal->getItemByColumnValue($column, $value))
			{
				return
				'<img class="alt-img" src="' . Mage::helper('infortis/image')->getImg($product, $w, $h, $imgVersion, $altImg->getFile()) . '" alt="' . $product->getName() . '" />';
			}
		}

		return '';
	}
	
	/**
	 * Returns true, if color is specified and the value doesn't equal "transparent"
	 *
	 * @param string $color color code
	 * @return bool
	 */
	public function isColor($color)
	{
		if ($color && $color != 'transparent')
			return true;
		else
			return false;
	}

	/**
	 * Get HTML of all child blocks with given ID
	 *
	 * @param $block Current block object
	 * @param string $staticBlockId ID of static blocks
	 * @param bool $auto Automatically align static blocks vertically
	 * @return string HTML output
	 */
	public function getFormattedBlocks($block, $staticBlockId, $auto = true)
	{
		//Get HTML output of 6 static blocks with ID $staticBlockId<X>, where <X> is a number from 1 to 6
		$colCount = 0; //Number of existing static blocks
		$colHtml = array(); //Static blocks content
		$html = ''; //Final HTML output
		for ($i = 1; $i < 7; $i++)
		{
			if ($tmp = $block->getChildHtml($staticBlockId . $i))
			{
				$colHtml[] = $tmp;
				$colCount++;
			}
		}
		
		if ($colHtml)
		{
			$gridClass = '';
			$gridClassBase = 'grid12-';
			$gridClassPersistent = ''; //'mobile-grid';
			
			//Get grid unit class
			if ($auto)
			{
				//Grid units per static block
				$n = (int) (12 / $colCount);
				$gridClass = $gridClassBase . $n;
			}
			else
			{
				$gridClass = $gridClassBase . '2';
			}
				
			for ($i = 0; $i < $colCount; $i++)
			{
				$classString = $gridClass; //. ($i==0?' alpha':'') . ($i==$colCount-1?' omega':'');
				$html .= '<div class="'. $classString .'">';
				$html .= '	<div class="std">'. $colHtml[$i] .'</div>';
				$html .= '</div>';
			}
		}
		return $html;
	}

	/**
	 * Returns path of the related products template file
	 *
	 * @return string
	 */
	public function getRelatedProductsTemplate()
	{
		return $this->getCfg('product_page/related_template');
	}
	
	/**
	 * Get theme's additional body CSS classes
	 * Credits: based on part of the PHP CSS Browser Selector by Bastian Allgeier http://bastian-allgeier.de/css_browser_selector
	 * which is a php port from Rafael Lima's CSS Browser Selector http://rafael.adm.br/css_browser_selector
	 *
	 * @return string CSS classes
	 */
	public function getThemeBodyClasses()
	{
		$classes = '';

		if (array_key_exists('HTTP_USER_AGENT', $_SERVER))
		{
			$array = array();
			$userAgentStr = strtolower($_SERVER['HTTP_USER_AGENT']);
			if (!preg_match('/opera|webtv/i', $userAgentStr) && preg_match('/msie\s(\d)/', $userAgentStr, $array))
			{
				if ($array[1] >= 6 && $array[1] <= 8)
				{
					$classes = 'lte-ie8';
				}
			}
		}
		
		return $classes;
	}
}
