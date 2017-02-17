<?php
/**
 * @category    Bubble
 * @package     Bubble_Elasticsearch
 * @version     4.1.2
 * @copyright   Copyright (c) 2016 BubbleShop (https://www.bubbleshop.net)
 */
class Bubble_Elasticsearch_Block_Autocomplete_Product_Price extends Bubble_Elasticsearch_Block_Autocomplete_Abstract
{
    /**
     * @var string
     */
    protected $_template = 'bubble/elasticsearch/autocomplete/product/price.phtml';

    /**
     * @return Zend_Currency
     */
    public function getCurrency()
    {
        if ($this->_config) {
            $currency = @unserialize($this->_config->getValue('currency_object'));
            if ($currency instanceof Zend_Currency) {
                return $currency;
            }
        }

        return new Zend_Currency();
    }

    /**
     * @return float
     */
    public function getCurrencyRate()
    {
        return $this->_config->getValue('currency_rate', 1);
    }

    /**
     * @param float $price
     * @return string
     * @throws Zend_Currency_Exception
     */
    public function formatPrice($price)
    {
        $price = sprintf('%F', $price);
        if ($price == -0) {
            $price = 0;
        }

        return $this->getCurrency()->toCurrency($price * $this->getCurrencyRate());
    }
}