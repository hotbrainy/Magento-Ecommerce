<?php
/**
 * @deprecated since 1.13.0
 */

class Infortis_Ultimo_Helper_Header extends Mage_Core_Helper_Abstract
{
	/**
	 * Menu module name
	 *
	 * @var string
	 */
	protected $_menuModuleName = 'Infortis_UltraMegamenu';
	protected $_menuModuleNameShort = 'ultramegamenu';

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
}
