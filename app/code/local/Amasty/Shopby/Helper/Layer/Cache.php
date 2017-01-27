<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


class Amasty_Shopby_Helper_Layer_Cache extends Amasty_Shopby_Helper_Cached
{
    const LIFETIME_NORMAL = 86400;
    const LIFETIME_SESSION = 1800;

    protected $data;
    protected $stateKey;
    protected $lifetime = self::LIFETIME_NORMAL;
    protected $isModified = false;

    public function setStateKey($stateKey)
    {
        $this->stateKey = $stateKey;
    }

    public function limitLifetime($lifetime) {
        $this->lifetime = min($lifetime, $this->lifetime);
    }

    public function setFilterItems($code, array $items)
    {
//        echo "SET " . $code .'<br>';
        if (!is_array($this->data)) {
            $this->data = array();
        }
        $this->data[$code] = $items;
        $this->isModified = true;
    }

    /**
     * @param $code
     * @return array|null
     */
    public function getFilterItems($code)
    {
        if (!is_array($this->data)) {
            $this->loadLayerCache();
        }

        $result = array_key_exists($code, $this->data) ? $this->data[$code] : null;
//        echo "GET " . $code . isset($result).'<br>';

        return $result;
    }

    public function saveLayerCache()
    {
        if (!$this->isModified) {
            return;
        }
//        echo "SAVE ". count($this->data) . ' ON ' . $this->lifetime .  '<br>';
        $this->save($this->data, $this->getKey(), $this->lifetime);
    }

    protected function loadLayerCache()
    {
        $key = $this->getKey();
        $this->data = $this->load($key);
        if (!is_array($this->data)) {
            $this->data = array();
        }
//        echo "LOADED " . count($this->data).'<br>';
        $this->isModified = false;
    }

    protected function getKey()
    {
        if (!$this->stateKey) {
            throw new Exception('Layer State Key not defined');
        }

        $landing = Mage::app()->getRequest()->getParam('am_landing', '');

        if (Mage::app()->getRequest()->getModuleName() == 'amshopby') {
            /** @var Amasty_Shopby_Helper_Attributes $attributesHelper */
            $attributesHelper = Mage::helper('amshopby/attributes');
            $brand = $attributesHelper->getRequestedBrandOption();
            $branding = $brand ? '_BRAND_' . $brand->getId() : '';
        } else {
            $branding = '';
        }

        $finder = '';
        if(Mage::helper('core')->isModuleEnabled('Amasty_Finder')) {
            $finderUrl = Mage::app()->getRequest()->getParam('find');
            if($finderUrl) {
                $param = Mage::getModel('amfinder/finder')->parseUrlParam($finderUrl);
                if(is_array($param)) {
                    $param = $param[count($param)-1];
                }
                if($param) {
                    $finder = "_AMFINDER_{$param}";
                }

            }
        }


        $key = $this->stateKey . $landing . $branding . $finder . '_AMSHOPBY';

        return $key;
    }
}
