<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_AttributeSplash_Controller_Adminhtml_Abstract extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Determine ACL permissions
	 *
	 * @return bool
	 */
	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed(
			'attributeSplash/dashboard'
		)
		|| Mage::getSingleton('admin/session')->isAllowed(
			'attributesplash/dashboard'
		);
	}
}
