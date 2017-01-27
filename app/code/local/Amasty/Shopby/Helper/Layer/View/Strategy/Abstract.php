<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

abstract class Amasty_Shopby_Helper_Layer_View_Strategy_Abstract
{
    /** @var Mage_Catalog_Block_Layer_Filter_Abstract */
    protected $filter;

    /** @var Amasty_Shopby_Block_Catalog_Layer_View */
    protected $layer;

    abstract protected function setTemplate();
    abstract protected function setPosition();
    abstract protected function setCollapsed();
    abstract protected function setHasSelection();

    public function setFilter(Mage_Catalog_Block_Layer_Filter_Abstract $filter)
    {
        $this->filter = $filter;
    }

    public function setLayer(Amasty_Shopby_Block_Catalog_Layer_View $layer)
    {
        $this->layer = $layer;
    }

    public function prepare()
    {
        $this->filter->setTemplate($this->setTemplate());
        $this->filter->setCollapsed($this->setCollapsed());
        $this->filter->setHasSelection($this->setHasSelection());
        $this->filter->setPosition($this->setPosition());
        $this->filter->setData('hide_counts', !$this->_getDataHelper()->getIsCountGloballyEnabled());
    }

    public function getIsExcluded()
    {
        return false;
    }

    protected function getCurrentCategoryId()
    {
        return $this->layer->getLayer()->getCurrentCategory()->getId();
    }

    protected function isCollapseEnabled()
    {
        return Mage::getStoreConfig('amshopby/general/enable_collapsing');
    }

    protected function _getDataHelper()
    {
        /** @var Amasty_Shopby_Helper_Data $helper */
        $helper = Mage::helper('amshopby');
        return $helper;
    }
}
