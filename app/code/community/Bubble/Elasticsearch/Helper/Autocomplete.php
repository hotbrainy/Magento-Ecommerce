<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Helper_Autocomplete extends Bubble_Elasticsearch_Helper_Data
{
    /**
     * @param mixed $store
     * @return int
     */
    public function getAutocompleteLimit($store = null)
    {
        return Mage::getStoreConfig('elasticsearch/autocomplete/limit', $store);
    }

    /**
     * @param mixed $store
     * @return array
     */
    public function getLabels($store = null)
    {
        $labels = array();
        $config = @unserialize(Mage::getStoreConfig('elasticsearch/autocomplete/labels', $store));
        if (is_array($config)) {
            foreach ($config as $data) {
                $labels[$data['label']] = $data['translation'];
            }
        }

        return $labels;
    }

    /**
     * Checks if autocomplete is enabled/available for given entity and store
     *
     * @param string $entity
     * @param mixed $store
     * @return bool
     */
    public function isAutocompleteEnabled($entity, $store = null)
    {
        return $this->isIndexationEnabled($entity, $store) &&
            Mage::getStoreConfigFlag('elasticsearch/'. $entity .'/enable_autocomplete', $store);
    }

    /**
     * Checks if autocomplete is enabled for given store
     *
     * @param mixed $store
     * @return bool
     */
    public function isGlobalAutocompleteEnabled($store = null)
    {
        return Mage::getStoreConfigFlag('elasticsearch/autocomplete/enable', $store);
    }

    /**
     * Checks if fast autocomplete is enabled for given store
     *
     * @param mixed $store
     * @return bool
     */
    public function isFastAutocompleteEnabled($store = null)
    {
        return $this->isGlobalAutocompleteEnabled($store) &&
            Mage::getStoreConfigFlag('elasticsearch/autocomplete/enable_fast', $store);
    }

    /**
     * Save Elasticsearch configuration so it can be used for fast autocomplete
     */
    public function saveConfig()
    {
        $config = array();
        $currentStore = Mage::app()->getStore();

        foreach (Mage::app()->getStores() as $store) {
            /** @var Mage_Core_Model_Store $store */
            if ($store->getIsActive()) {
                Mage::app()->setCurrentStore($store);

                $locale = Mage::getSingleton('core/locale');
                $locale->emulate($store->getId());
                $currency = Mage::app()->getLocale()->currency($store->getCurrentCurrencyCode());

                $config[$store->getCode()] = array(
                    'client_config'     => $this->getEngineConfigData($store),
                    'config'            => Mage::getStoreConfig('elasticsearch', $store),
                    'analyzers'         => $this->getStoreAnalyzers($store),
                    'currency_object'   => serialize($currency),
                    'currency_rate'     => floatval($store->getBaseCurrency()->getRate($store->getCurrentCurrencyCode())),
                    'base_url'          => $store->getUrl('/'),
                    'package'           => Mage::getStoreConfig('design/package/name', $store),
                    'theme'             => Mage::getStoreConfig('design/theme/template', $store),
                );

                foreach ($this->getStoreTypes($store) as $type) {
                    if (!$this->isAutocompleteEnabled($type, $store)) {
                        continue;
                    }

                    /** @var Bubble_Elasticsearch_Helper_Indexer_Abstract $indexer */
                    $indexer = Mage::helper('elasticsearch/indexer_' . $type);
                    $config[$store->getCode()]['types'][$type] = array(
                        'block'             => $indexer->getBlockClass(),
                        'index_properties'  => $indexer->getStoreIndexProperties($store),
                        'additional_fields' => $indexer->getAdditionalFields(),
                    );
                }
            }
        }

        Mage::dispatchEvent('bubble_elasticsearch_autocomplete_save_config_before', array('config' => &$config));

        Bubble_Elasticsearch_Config::save($config);

        Mage::app()->setCurrentStore($currentStore);
    }
}