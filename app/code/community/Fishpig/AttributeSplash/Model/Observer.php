<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Model_Observer
{
	/**
	 * Inject links into the top navigation
	 *
	 * @param Varien_Event_Observer $observer
	 * @return $this
	 */
	public function injectTopmenuLinksObserver(Varien_Event_Observer $observer)
	{	
		if (Mage::getStoreConfigFlag('attributeSplash/navigation/enabled')) {
			$groups = Mage::getResourceModel('attributeSplash/group_collection')
				->addStoreFilter(Mage::app()->getStore()->getId())
				->addOrderByName()
				->load();
				
			$this->_injectLinks($groups, $observer->getEvent()->getMenu());
		}
		
		return $this;
	}
	
	/**
	 * Inject links into the top navigation
	 *
	 * @param Mage_Core_Model_Resource_Db_Collection_Abstract $items
	 * @param Varien_Data_Tree_Node $parentNode
	 * @return bool
	 */
	protected function _injectLinks($items, $parentNode)
	{
		if (!$parentNode) {
			return false;	
		}
		
		foreach($items as $item) {
			if (!$item->canIncludeInMenu()) {
				continue;
			}

			$data = array(
				'name' => $item->getName(),
				'id' => $item->getMenuNodeId(),
				'url' => $item->getUrl(),
				'is_active' => $item->isActive(),
			);
			
			if ($data['is_active']) {
				$parentNode->setIsActive(true);
				$buffer = $parentNode;
				
				while($buffer->getParent()) {
					$buffer = $buffer->getParent();
					$buffer->setIsActive(true);
				}
			}
			
			$itemNode = new Varien_Data_Tree_Node($data, 'id', $parentNode->getTree(), $parentNode);
			$parentNode->addChild($itemNode);

			$children = $item->getSplashPages();

			if ($children) {
				$children->addOrderByName()
					->addFieldToFilter('include_in_menu', 1)
					->load();

				$this->_injectLinks($children, $itemNode);
			}
		}
		
		return true;
	}

	/**
	 * Remove the e.visibility where part
	 *
	 * @param Varien_Event_Observer $observer
	 * @return $this
	 */
	public function prepareCatalogPriceSelectObserver(Varien_Event_Observer $observer)
	{
		$where = $observer->getEvent()
			->getSelect()
				->getPart(Zend_Db_Select::WHERE);
		
		foreach($where as $key => $value) {
			if (strpos($value, 'e.visibility') !== false) {
				unset($where[$key]);
				break;
			}
		}
		
		$observer->getEvent()
			->getSelect()
				->setPart(Zend_Db_Select::WHERE, $where);
		
		return $this;
	}
	
	/**
	 * Add support for FishPig_FSeo
	 *
	 * @param Varien_Event_Observer $observer
	 * @return $this|bool
	 */
	public function fseoLayeredNavigationMatchEntityObserver(Varien_Event_Observer $observer)
	{
		if (!Mage::helper('fseo/layer')->isEntityTypeEnabled('attributeSplash_page')) {
			return $this;
		}
	
		$doubleBarrel = Mage::getStoreConfigFlag('attributeSplash/page/include_group_url_key');
		
		$urlKey = $observer->getEvent()->getRequestUri();	
		$urlSuffix = Fishpig_AttributeSplash_Model_Page::getUrlSuffix();

    	if ($urlSuffix && $urlSuffix !== '/') {
			if (substr($urlKey, -strlen($urlSuffix)) !== $urlSuffix) {
				return false;
			}
			
			$urlKey = substr($urlKey, 0, -strlen($urlSuffix));
    	}

		$expectedSlashCount = 1 + (int)$doubleBarrel;

    	if (substr_count($urlKey, '/') < $expectedSlashCount) {
	    	return false;
    	}
		
		if ($doubleBarrel) {
			$baseUrlKey = substr($urlKey, 0, strpos($urlKey, '/', strpos($urlKey, '/')+1));
			list($groupUrlKey, $pageUrlKey) = explode('/', $baseUrlKey);
		}
		else {
	    	$baseUrlKey = substr($urlKey, 0, strpos($urlKey, '/'));
	    	$groupUrlKey = null;
	    	$pageUrlKey = $baseUrlKey;
	    }

		$splashIds = Mage::getResourceModel('attributeSplash/page')->getPageAndGroupIdByUrlKeys($pageUrlKey, $groupUrlKey);
		
		if (!$splashIds) {
			return false;
		}
		
		if (count($splashIds)) {
			$splashIds[] = null;
		}

		list($pageId, $groupId) = array_values($splashIds);
		
		$tokens = explode('/', trim(substr($urlKey, strlen($baseUrlKey)), '/'));

		$observer->getEvent()->getTransport()
			->setEntityData(
				new Varien_Object(array(
					'entity_id' => $pageId,
					'entity_type' => 'attributeSplash_page',
					'entity_url_key' => $baseUrlKey,
					'url_suffix' => $urlSuffix,
					'tokens' => $tokens,
					'module_name' => 'splash',
					'controller_name' => 'page',
					'action_name' => 'view',
					'params' => array(
						'id' => $pageId,
						'group_id' => $groupId,
					)
				)));
		
		Mage::helper('attributeSplash')->clearLayerRewrites();
		
		return $this;
	}
}
