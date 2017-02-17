<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

class Amasty_Shopby_Helper_Layer_View_Strategy_Attribute extends Amasty_Shopby_Helper_Layer_View_Strategy_Modeled
{
    public function prepare()
    {
        parent::prepare();

        $this->prepareItems();
    }

    protected function setTemplate()
    {
        $template = 'amasty/amshopby/attribute.phtml';

        $isSwatchesDisplayType = is_object($this->model) && $this->model->getDisplayType() == Amasty_Shopby_Model_Source_Attribute::DT_MAGENTO_SWATCHES;
        if ($isSwatchesDisplayType) {
            if ($this->isSwatchesAvailable()) {
                $template = 'configurableswatches/catalog/layer/filter/swatches.phtml';
            } else {
                $this->model->setDisplayType(Amasty_Shopby_Model_Source_Attribute::DT_LABELS_ONLY);
            }
        }

        return $template;
    }

    protected function isSwatchesAvailable()
    {
        if (Mage::helper('amshopby')->isModuleEnabled('Mage_ConfigurableSwatches')) {
            /** @var Mage_ConfigurableSwatches_Helper_Data $configurableSwatchesHelper */
            $configurableSwatchesHelper = Mage::helper('configurableswatches');
            if ($configurableSwatchesHelper->isEnabled()) {
                if ($configurableSwatchesHelper->attrIsSwatchType($this->attribute->getId())) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function setHasSelection()
    {
        $selected = $this->getSelectedValues();
        return !empty($selected);
    }

    protected function prepareItems()
    {
        $items = $this->filter->getItems();

        $options = $this->layer->getAttributeOptionsData();

        foreach ($items as $item){
            /** @var Amasty_Shopby_Model_Catalog_Layer_Filter_Item $item */

            $optId = $item->getOptionId();
            $item->setIsSelected(in_array($optId, $this->getSelectedValues()));

            if (!empty($options[$optId]['img'])){
                $item->setImage($options[$optId]['img']);
            }
            if (!empty($options[$optId]['img_hover'])){
                $item->setImageHover($options[$optId]['img_hover']);
                if ($item->getIsSelected()) {
                    $item->setImage($options[$optId]['img_hover']);
                }
            }
            if (!empty($options[$optId]['descr'])){
                $item->setDescr($options[$optId]['descr']);
            }
        }
    }

    public function getSelectedValues()
    {
        $selectedValues = $this->_getDataHelper()->getRequestValues($this->attribute->getAttributeCode());
        return $selectedValues;
    }

    protected function getTransferableFields()
    {
        return array('max_options', 'sort_by', 'sort_featured_first', 'display_type', 'single_choice', 'seo_rel', 'depend_on_attribute', 'comment', 'show_search', 'number_options_for_show_search');
    }

    public function getIsExcluded()
    {
        if (parent::getIsExcluded()) {
            return true;
        }

        // hide when selected
        $hideBySingleChoice = (defined('AMSHOPBY_FEATURE_HIDE_SINGLE_CHOICE_FILTERS') && AMSHOPBY_FEATURE_HIDE_SINGLE_CHOICE_FILTERS && $this->model->getSingleChoice());
        $hideByConfigurableSwatches = $this->model->getDisplayType() == Amasty_Shopby_Model_Source_Attribute::DT_MAGENTO_SWATCHES && $this->isSwatchesAvailable();

        if ($hideBySingleChoice || $hideByConfigurableSwatches) {
            if (Mage::app()->getRequest()->getParam($this->attribute->getAttributeCode())) {
                return true;
            }
        }

        return false;
    }
}
