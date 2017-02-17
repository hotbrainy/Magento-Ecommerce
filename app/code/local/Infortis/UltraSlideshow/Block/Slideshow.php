<?php
class Infortis_UltraSlideshow_Block_Slideshow extends Mage_Core_Block_Template
{
	protected $_isPredefinedHomepageSlideshow = false;
	protected $_slides = NULL;
	protected $_banners = NULL;
	protected $_cacheKeyArray = NULL;
	protected $_coreHelper;

	/**
	 * Initialize block's cache
	 */
	protected function _construct()
	{
		parent::_construct();

		$this->_coreHelper = Mage::helper('ultraslideshow');

		$this->addData(array(
			'cache_lifetime'    => 99999999,
			'cache_tags'        => array(Mage_Catalog_Model_Product::CACHE_TAG),
		));
	}

	/**
	 * Get Key pieces for caching block content
	 *
	 * @return array
	 */
	public function getCacheKeyInfo()
	{
		if (NULL === $this->_cacheKeyArray)
		{
			$this->_cacheKeyArray = array(
				'INFORTIS_ULTRASLIDESHOW',
				Mage::app()->getStore()->getId(),
				Mage::getDesign()->getPackageName(),
				Mage::getDesign()->getTheme('template'),
				Mage::getSingleton('customer/session')->getCustomerGroupId(),
				'template' => $this->getTemplate(),
				'name' => $this->getNameInLayout(),
				(int)Mage::app()->getStore()->isCurrentlySecure(),
				implode(".", $this->getSlideIds()),
				$this->getBanner(),
				$this->_isPredefinedHomepageSlideshow,
			);
		}
		return $this->_cacheKeyArray;
	}

	/**
	 * Create unique block id for frontend
	 *
	 * @return string
	 */
	public function getFrontendHash()
	{
		return md5(implode("+", $this->getCacheKeyInfo()));
	}

	/**
	 * Get array of slides (static blocks) identifiers. Blocks will be displayed as slides.
	 *
	 * @return array|NULL
	 */
	public function getSlideIds()
	{
		$slides = NULL;
		if ($this->_slides)
		{
			return $this->_slides;
		}
		else //No predefined slides
		{
			//Get slides from parameter
			$slides = $this->getParamStaticBlockIds();
			if (empty($slides))
			{
				//If this is predefined slideshow, get slides from module config
				if ($this->_isPredefinedHomepageSlideshow)
				{
					$slides = $this->getConfigStaticBlockIds();
				}
			}
		}

		//Retrieved slides can be saved for further processing
		$this->_slides = $slides;
		return $slides;
	}

	/**
	 * Get array of static blocks identifiers from parameter
	 *
	 * @return array|NULL
	 */
	public function getParamStaticBlockIds()
	{
		$slides = $this->getSlides(); //param: slides
		if ($slides === NULL) //Param not set
		{
			return array();
		}

		$blockIds = explode(",", str_replace(" ", "", $slides));
		return $blockIds;
	}

	/**
	 * Get array of static blocks identifiers from module config
	 *
	 * @return array
	 */
	public function getConfigStaticBlockIds()
	{
		$blockIds = NULL;
		$blockIdsString = $this->_coreHelper->getCfg('general/blocks');
		$blockIdsString = trim($blockIdsString);

		if (!empty($blockIdsString))
		{
			$blockIds = explode(",", str_replace(" ", "", $blockIdsString));
		}

		return $blockIds;
	}

	/**
	 * Get HTML of the static block which contains additional banners for the slideshow
	 *
	 * @return string
	 */
	public function getBannersHtml()
	{
		$bid = '';
		if ($this->_banners)
		{
			$bid = $this->_banners;
		}
		else //No predefined banners
		{
			//Get banners from parameter
			$bid = $this->getBanner(); //param: banner
			if ($bid === NULL) //Param not set
			{
				//If this is predefined slideshow, get banners from module config
				if ($this->_isPredefinedHomepageSlideshow)
				{
					//Get banners from module config
					$bid = $this->_coreHelper->getCfg('banners/banners');
				}
			}
		}

		//If banner id specified
		$bid = trim($bid);
		if ($bid)
		{
			return $this->getLayout()->createBlock('cms/block')->setBlockId($bid)->toHtml();
		}
		return '';
	}

	/**
	 * Add slides ids
	 *
	 * @param string $ids
	 * @return Infortis_UltraSlideshow_Block_Slideshow
	 */
	public function addSlides($ids)
	{
		$this->_slides = $ids;
		return $this;
	}

	/**
	 * Add banner id
	 *
	 * @param string $ids
	 * @return Infortis_UltraSlideshow_Block_Slideshow
	 */
	public function addBanner($ids)
	{
		$this->_banners = $ids;
		return $this;
	}

	/**
	 * Set/Unset as predefined slideshow (e.g. for homepage)
	 *
	 * @param string $value
	 * @return Infortis_UltraSlideshow_Block_Slideshow
	 */
	public function setPredefined($value)
	{
		$this->_isPredefinedHomepageSlideshow = $value;
		return $this;
	}

	/**
	 * Check if slideshow is set as predefined
	 *
	 * @return bool
	 */
	public function isPredefined()
	{
		return $this->_isPredefinedHomepageSlideshow;
	}

	/**
	 * Get CSS style string with margins for slideshow wrapper
	 *
	 * @return string
	 */
	public function getMarginStyles()
	{
		//Slideshow margin
		$slideshowMarginStyleProperties = '';

		$marginTop = intval($this->_coreHelper->getCfg('general/margin_top'));
		if ($marginTop !== 0)
		{
			$slideshowMarginStyleProperties .= "margin-top:{$marginTop}px;";
		}

		$marginBottom = intval($this->_coreHelper->getCfg('general/margin_bottom'));
		if ($marginBottom !== 0)
		{
			$slideshowMarginStyleProperties .= "margin-bottom:{$marginBottom}px;";
		}

		if ($slideshowMarginStyleProperties)
		{
			return 'style="' . $slideshowMarginStyleProperties . '"';
		}
	}

	/**
	 * If slideshow position retrieved from config is different than expected position, set flag to not display the slideshow
	 *
	 * @param int $position
	 * @param int $expectedPosition
	 * @return Infortis_UltraSlideshow_Block_Slideshow
	 */
	/*
	public function displayOnExpectedPosition($position, $expectedPosition)
	{
		if ($position !== $expectedPosition)
		{
			$this->_canBeDisplayed = false;
		}
		return $this;
	}
	*/

	/**
	 * @deprecated
	 * Get slideshow config
	 *
	 * @return string
	 */
	public function getSlideshowCfg()
	{
		$h = $this->_coreHelper;
		
		$cfg = array();
		$cfg['fx']			= "'" . $h->getCfg('general/fx') . "'";
		
		if ($h->getCfg('general/easing'))
		{
			$cfg['easing']	= "'" . $h->getCfg('general/easing') . "'";
		}
		else
		{
			$cfg['easing']	= '';
		}
		
		$cfg['timeout']			= intval($h->getCfg('general/timeout'));
		$cfg['speed']			= intval($h->getCfg('general/speed'));
		$cfg['smooth_height']	= $h->getCfg('general/smooth_height');
		$cfg['pause']			= $h->getCfg('general/pause');
		$cfg['loop']			= $h->getCfg('general/loop');
		
		return $cfg;
	}
}