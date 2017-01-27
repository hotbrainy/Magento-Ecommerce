<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

class Amasty_Shopby_Helper_Layer_View_Strategy_Price extends Amasty_Shopby_Helper_Layer_View_Strategy_Abstract
{
    public function prepare()
    {
        parent::prepare();

        $this->filter->setDisplayType(Mage::getStoreConfig('amshopby/general/price_type'));
        $this->filter->setSliderType(Mage::getStoreConfig('amshopby/general/slider_type'));

        $step = Mage::getStoreConfig('amshopby/general/slider_step');
        if ($step <= 0) {
            $step = 1.0;
        }
        $this->filter->setSliderDecimal($step);

        $currencySign = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
        $this->filter->setValueLabel($currencySign);

        $this->filter->setValuePlacement('before');
        $this->filter->setFromToWidget(Mage::getStoreConfig('amshopby/general/price_from_to'));
        $this->filter->setAttributeCode('price');
        $this->filter->setSeoRel($this->_getDataHelper()->getSeoPriceRelNofollow());
    }

    protected function setTemplate()
    {
        return 'amasty/amshopby/price.phtml';
    }

    protected function setPosition()
    {
        return $this->filter->getAttributeModel()->getPosition();
    }

    protected function setHasSelection()
    {
        return Mage::app()->getRequest()->getParam('price');
    }

    protected function setCollapsed()
    {
        return $this->isCollapseEnabled() && Mage::getStoreConfig('amshopby/general/price_collapsed');
    }

    public function getIsExcluded()
    {
        if (parent::getIsExcluded()) {
            return true;
        }
        $excludeCats = explode(',', Mage::getStoreConfig('amshopby/general/price_exclude'));
        $helper = Mage::helper('amshopby');
        if (in_array( $helper->getCurrentCategory()->getId() ,$excludeCats ) ){
            return true;
        }
        return false;
    }
}
