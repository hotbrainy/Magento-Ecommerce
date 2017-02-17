<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Helper_Catalogsearch extends Mage_CatalogSearch_Helper_Data
{
    /**
     * @return string
     */
    public function getSuggestUrl()
    {
        $url = parent::getSuggestUrl();

        $helper = Mage::helper('elasticsearch/autocomplete');
        if ($helper->isActiveEngine() && $helper->isFastAutocompleteEnabled()) {
            $url = sprintf('%sautocomplete.php?store=%s&fallback_url=%s',
                Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, Mage::app()->getStore()->isCurrentlySecure()),
                Mage::app()->getStore()->getCode(),
                $url
            );
        }

        return $url;
    }

    /**
     * Get Elasticsearch engine
     *
     * @return Bubble_Elasticsearch_Model_Resource_Engine
     */
    public function getEngine()
    {
        return Mage::getResourceSingleton('elasticsearch/engine');
    }
}