<?php
/**
 * @category    Fishpig
 * @package     Fishpig_AttributeSplash
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_AttributeSplash_Block_Page_View_Product_List extends Mage_Catalog_Block_Product_List
{
	/**
	 * Retrieves the current layer product collection
	 *
	 * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
	 */
	protected function _getProductCollection()
	{
		if (is_null($this->_productCollection)) {
			$this->_productCollection = Mage::getSingleton('attributeSplash/layer')->getProductCollection();

			if ($orders = Mage::getSingleton('catalog/config')->getAttributeUsedForSortByArray()) {
				if (isset($orders['position'])) {
					unset($orders['position']);
				}
				
				$this->setAvailableOrders($orders);

				if (!$this->getSortBy()) {
					$category = Mage::getModel('catalog/category')->setStoreId(
						Mage::app()->getStore()->getId()
					);

					$this->setSortBy($category->getDefaultSortBy());
				}
			}
		}
		
		return $this->_productCollection;
	}
}
