<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_SitemapController extends Mage_Core_Controller_Front_Action
{
	/**
	 * Display the splash page
	 *
	 * @return void
	 */
	public function viewAction()
	{
		try {
			$xmlSitemapBlock = Mage::getSingleton('core/layout')->createBlock('attributeSplash_addon_xmlsitemap/sitemap');
			
			if (!$xmlSitemapBlock) {
				throw new Exception('Required add-on (http://fishpig.co.uk/magento/extensions/attribute-splash-pages/xml-sitemap/) not installed.');
			}
			
			$output = $xmlSitemapBlock->toHtml();

			return $this->getResponse()
				->setHeader('Content-Type', 'text/xml')
				->setBody($output);
		}
		catch (Exception $e) {
			Mage::log($e->getMessage());
		}
		
		return $this->_forward('noRoute');
	}
}
