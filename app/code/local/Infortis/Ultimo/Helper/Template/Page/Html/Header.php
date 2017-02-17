<?php

class Infortis_Ultimo_Helper_Template_Page_Html_Header extends Mage_Core_Helper_Abstract
{
	/**
	 * Menu module name
	 *
	 * @var string
	 */
	protected $_menuModuleName = 'Infortis_UltraMegamenu';
	protected $_menuModuleNameShort = 'ultramegamenu';

	/**
	 * Theme helper
	 *
	 * @var Infortis_Ultimo_Helper_Data
	 */
	protected $_theme;

	/**
	 * Positions of header blocks
	 *
	 * @var array
	 */
	protected $_pos;

	/**
	 * Resource initialization
	 */
	public function __construct()
	{
		$this->_theme = Mage::helper('ultimo');

		$this->_pos['logo']			= $this->_theme->getCfg('header/logo_position');
		$this->_pos['search']		= $this->_theme->getCfg('header/search_position');
		$this->_pos['user-menu']	= $this->_theme->getCfg('header/user_menu_position');
		$this->_pos['compare']		= $this->_theme->getCfg('header/compare_position');
		$this->_pos['cart']			= $this->_theme->getCfg('header/cart_position');
		$this->_pos['main-menu']	= $this->_theme->getCfg('header/main_menu_position');
	}

	/**
	 * Get mobile menu threshold from the menu module.
	 * If module not enabled, return NULL.
	 *
	 * @return string
	 */
	public function getMobileMenuThreshold()
	{
		if(Mage::helper('core')->isModuleEnabled($this->_menuModuleName))
		{
			return Mage::helper($this->_menuModuleNameShort)->getMobileMenuThreshold();
		}
		return NULL;
	}

	/**
	 * Get positions of header blocks
	 *
	 * @return array
	 */
	public function getPositions()
	{
		return $this->_pos;
	}

	/**
	 * Create grid classes for header sections
	 *
	 * @return array
	 */
	public function getGridClasses()
	{
		//Width (in grid units) of product page sections
		$primLeftColUnits		= $this->_theme->getCfg('header/left_column');
		$primCentralColUnits	= $this->_theme->getCfg('header/central_column');
		$primRightColUnits		= $this->_theme->getCfg('header/right_column');

		//Grid classes
		$classPrefix = 'grid12-';

		if (!empty($primLeftColUnits) && trim($primLeftColUnits) !== '')
		{
			$grid['primLeftCol'] 		= $classPrefix . $primLeftColUnits;
		}

		if (!empty($primCentralColUnits) && trim($primCentralColUnits) !== '')
		{
			$grid['primCentralCol']		= $classPrefix . $primCentralColUnits;
		}

		if (!empty($primRightColUnits) && trim($primRightColUnits) !== '')
		{
			$grid['primRightCol']		= $classPrefix . $primRightColUnits;
		}

		return $grid;
	}

	/**
	 * Check if main menu is displayed inisde a section (full-width section) at the bottom of the header
	 *
	 * @return bool
	 */
	public function isMenuDisplayedInFullWidthContainer()
	{
		if ($this->_pos['main-menu'] === 'menuContainer')
		{
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Get array of flags indicating if blocks are displayed directly inside the header block template (TRUE)
	 * or inside one of the child blocks (FALSE).
	 *
	 * @return array
	 */
	public function getDisplayedInHeaderBlock()
	{
		//List of blocks that are displayed directly inside the header block template.
		//Important: it can contain only the blocks which can be optionally added to the User Menu.
		$display = array();
		$display['search']	= TRUE;
		$display['cart']	= TRUE;
		$display['compare']	= TRUE;

		if ($this->_pos['search'] === 'userMenu' || $this->_pos['search'] === 'mainMenu')
		{
			$display['search'] = FALSE;
		}

		if ($this->_pos['cart'] === 'userMenu' || $this->_pos['cart'] === 'mainMenu')
		{
			$display['cart'] = FALSE;
		}

		if ($this->_pos['compare'] === 'userMenu' || $this->_pos['compare'] === 'mainMenu')
		{
			$display['compare'] = FALSE;
		}

		return $display;
	}

	/**
	 * Get array of flags indicating if blocks are displayed above the skip links (in the top of the header)
	 * and need to be moved below the skip links on mobile view.
	 *
	 * @return array
	 */
	public function getMoveBelowSkipLinks()
	{
		//List of blocks that need to be moved below the skip links on mobile view
		$move = array();
		$move['user-menu']	= FALSE;
		$move['search']		= FALSE;
		$move['compare']	= FALSE;

		//Check if blocks are displayed above the skip links
		//if ($this->_pos['search'] === 'topLeft_1' || $this->_pos['search'] === 'topRight_1')
		if ($this->_pos['search'] === 'topLeft_1' || $this->_pos['search'] === 'topRight_1' || $this->_pos['search'] === 'mainMenu')
		{
			$move['search'] = TRUE;
		}

		if ($this->_pos['compare'] === 'topLeft_1' || $this->_pos['compare'] === 'topRight_1' || $this->_pos['compare'] === 'mainMenu')
		{
			$move['compare'] = TRUE;
		}

		if ($this->_pos['user-menu'] === 'topLeft_1' || $this->_pos['user-menu'] === 'topRight_1')
		{
			$move['user-menu'] = TRUE;

			//If the User Menu is displayed above the skip links, check other blocks again.
			//If block is already inside the User Menu, it doesn't need to be moved below the skip links.
			if ($this->_pos['search'] === 'userMenu')
			{
				$move['search'] = FALSE;
			}
			if ($this->_pos['compare'] === 'userMenu')
			{
				$move['compare'] = FALSE;
			}
		}
		else
		{
			$move['user-menu'] = FALSE;
		}

		return $move;
	}

}
