<?php

class Infortis_Ultimo_Model_Observer
{
	/**
	 * Temporary fix for CMS blocks cache bug in Magento 1.9.2.0 (issue #7817 on Magento bug-tracking)
	 * Based on https://github.com/progammer-rkt/Rkt_SbCache by progammer-rkt
	 *
	 *
	 * 
	 * Use to apply caching for CMS Blocks in Magento.
	 * By default, Magento is not applying caching for CMS blocks. This function
	 * will apply cache for CMS Blocks and thus help us to overcome this difficulty.
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Infortis_Ultimo_Model_Observer
	 */
	public function enableCmsBlockCaching(Varien_Event_Observer $observer)
	{
		$block = $observer->getBlock();
		//make sure cache is going to apply for a cms block.
		if ($block instanceof Mage_Cms_Block_Widget_Block
			|| $block instanceof Mage_Cms_Block_Block
		) {
			//Mage::log('ID: '. Mage::app()->getStore()->getId() .' --> '. $block->getBlockId()  ); ///

			//making a unique cache key for each cms blocks so that cached HTML
			//content will be unique per each static block
			$cacheKeyData = array(
				Mage_Cms_Model_Block::CACHE_TAG,
				$block->getBlockId(),
				Mage::app()->getStore()->getId(),
				intval(Mage::app()->getStore()->isCurrentlySecure())
			);
			$block->setCacheKey(implode('_', $cacheKeyData));
			//set cache tags. This will help us to clear the cache related to
			//a static block based on store, CMS cache, or by identifier.
			$block->setCacheTags(array(
				Mage_Core_Model_Store::CACHE_TAG,
				Mage_Cms_Model_Block::CACHE_TAG,
				(string)$block->getBlockId()
			));
			//setting cache life time to default. ie 7200 seconds(2 hrs).
			//Other options are
			//    - an integer value in seconds. eg : 86400 for one day cache
			//    - NULL for not applying any cache
			//    - 0 for never use cache (strongly discourage use of of zero)
			$block->setCacheLifetime(false);
		}
		return $this;
	}
	//end:fix



	/**
	 * After any system config is saved
	 */
	public function hookTo_controllerActionPostdispatchAdminhtmlSystemConfigSave()
	{
		$section = Mage::app()->getRequest()->getParam('section');
		if ($section == 'ultimo_layout')
		{
			$websiteCode = Mage::app()->getRequest()->getParam('website');
			$storeCode = Mage::app()->getRequest()->getParam('store');

			$cg = Mage::getSingleton('ultimo/cssgen_generator');
			$cg->generateCss('grid',   $websiteCode, $storeCode);
			$cg->generateCss('layout', $websiteCode, $storeCode);
		}
		elseif ($section == 'ultimo_design')
		{
			$websiteCode = Mage::app()->getRequest()->getParam('website');
			$storeCode = Mage::app()->getRequest()->getParam('store');
			
			Mage::getSingleton('ultimo/cssgen_generator')->generateCss('design', $websiteCode, $storeCode);
		}
		elseif ($section == 'ultimo')
		{
			$websiteCode = Mage::app()->getRequest()->getParam('website');
			$storeCode = Mage::app()->getRequest()->getParam('store');
			
			Mage::getSingleton('ultimo/cssgen_generator')->generateCss('layout', $websiteCode, $storeCode);
		}
	}
	
	/**
	 * After store view is saved
	 */
	public function hookTo_storeEdit(Varien_Event_Observer $observer)
	{
		$store = $observer->getEvent()->getStore();
		if ($store->getIsActive())
		{
			$this->_onStoreChange($store);
		}
	}

	/**
	 * After store view is added
	 */
	public function hookTo_storeAdd(Varien_Event_Observer $observer)
	{
		$store = $observer->getEvent()->getStore();
		if ($store->getIsActive())
		{
			$this->_onStoreChange($store);
		}
	}

	/**
	 * On store view changed
	 */
	protected function _onStoreChange($store)
	{
		$storeCode = $store->getCode();
		$websiteCode = $store->getWebsite()->getCode();
		
		$cg = Mage::getSingleton('ultimo/cssgen_generator');
		$cg->generateCss('grid',   $websiteCode, $storeCode);
		$cg->generateCss('layout', $websiteCode, $storeCode);
		$cg->generateCss('design', $websiteCode, $storeCode);
	}

	/**
	 * After config import
	 */
	public function hookTo_DataporterCfgporterImportAfter(Varien_Event_Observer $observer)
	{
		$event = $observer->getEvent();
		$websiteCode 	= '';
		$storeCode 		= '';
		$scope = $event->getData('portScope');
		$scopeId = $event->getData('portScopeId');
		switch ($scope) {
			case 'websites':
				$websiteCode 	= Mage::app()->getWebsite($scopeId)->getCode();
				break;
			case 'stores':
				$storeCode 		= Mage::app()->getStore($scopeId)->getCode();
				$websiteCode 	= Mage::app()->getStore($scopeId)->getWebsite()->getCode();
				break;
		}
		
		Mage::app()->getConfig()->reinit();
		$cg = Mage::getSingleton('ultimo/cssgen_generator');
		$cg->generateCss('grid',   $websiteCode, $storeCode);
		$cg->generateCss('layout', $websiteCode, $storeCode);
		$cg->generateCss('design', $websiteCode, $storeCode);
	}
}
