<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Adminhtml_Xmlsitemap extends Mage_Core_Block_Text
{
	protected function _beforeToHtml()
	{
		$this->setText('You need the <a href="#">XML Sitemap</a> add-on.');
		
		return parent::_beforeToHtml();
	}
}
