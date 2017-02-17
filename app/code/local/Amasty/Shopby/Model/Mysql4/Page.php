<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

/**
 * @author Amasty
 */ 
class Amasty_Shopby_Model_Mysql4_Page extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('amshopby/page', 'page_id');
    }

    /**
     * @param int $categoryId
     * @return Amasty_Shopby_Model_Page|null
     */
    public function getCurrentMatchedPage($categoryId)
    {
        $result = null;

        /** @var Amasty_Shopby_Model_Mysql4_Page_Collection $collection */
        $collection = Mage::getModel('amshopby/page')->getCollection();
        $collection->addStoreFilter();
        if ($categoryId) {
            $collection->addCategoryFilter($categoryId);
        }

        foreach ($collection as $page){
            /** @var Amasty_Shopby_Model_Page $page */

            if ($page->matchCurrentFilters()) {
                $result = $page;
                break;
            }
        }

        return $result;
    }
}