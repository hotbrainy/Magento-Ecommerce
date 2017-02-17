<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


class Amasty_Shopby_Block_Catalog_Layer_Filter_Empty extends Mage_Catalog_Block_Layer_Filter_Abstract
{
    /**
     * Initialize filter template
     *
     */
    public function __construct()
    {
    }

    /**
     * Initialize filter model object
     *
     * @return Mage_Catalog_Block_Layer_Filter_Abstract
     */
    public function init()
    {
        return $this;
    }

    /**
     * Retrieve name of the filter block
     *
     * @return string
     */
    public function getName()
    {
        return '';
    }

    /**
     * Retrieve filter items
     *
     * @return array
     */
    public function getItems()
    {
        return array();
    }

    /**
     * Retrieve filter items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        return 0;
    }

    /**
     * For Enterprise Solr compatibility
     */
    public function addFacetCondition() {}
} 