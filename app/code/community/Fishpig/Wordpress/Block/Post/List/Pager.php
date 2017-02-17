<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_List_Pager extends Mage_Page_Block_Html_Pager 
{
	/**
	 * Construct the pager and set the limits
	 *
	 */
	protected function _construct()
	{
		parent::_construct();	

		$this->setPageVarName('page');

		$baseLimit = $this->helper('wordpress')->getWpOption('posts_per_page', 10);

		$this->setDefaultLimit($baseLimit);
		$this->setLimit($baseLimit);
		
		$this->setAvailableLimit(array(
			$baseLimit => $baseLimit,
		));
		
		$this->setFrameLength(5);
	}
	
	/**
	 * Return the URL for a certain page of the collection
	 *
	 * @return string
	 */
	public function getPagerUrl($params=array())
	{
		$pageVarName = $this->getPageVarName();

		$slug = isset($params[$pageVarName]) 
			? $pageVarName . '/' . $params[$pageVarName] . '/'
			: '';
		
		$slug = ltrim($slug, '/');

		$baseUrl = $this->getUrl('*/*/*', array(
			'_current' => true,
			'_escape' => true,
			'_use_rewrite' => true,
			'_nosid' => true,
			'_query' => array('___refresh' => null),
		));
		
		$queryString = '';
		
		if (strpos($baseUrl, '?') !== false) {
			$queryString = substr($baseUrl, strpos($baseUrl, '?'));
			$baseUrl = substr($baseUrl, 0, strpos($baseUrl, '?'));
		}
		
		return rtrim($baseUrl, '/') . '/' . $slug . $queryString;
	}
}
