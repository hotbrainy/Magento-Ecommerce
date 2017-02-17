<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


class Amasty_Shopby_Model_Url_Parser
{
    /** @var  string */
    protected $params;

    /** @var  array */
    protected $query;

    protected $optionChar;

    /**
     * @param string $params
     * @return array|false $query
     */
    public function parseParams($params)
    {
        $this->prepareInitialData($params);

        if ($this->params == Mage::getStoreConfig('amshopby/seo/key')) {
            return array();
        }

        while ($this->params != '') {
            $stepSuccess = false;
            $stepSuccess |= $this->matchPrice();
            $stepSuccess |= $this->matchDecimal();
            $stepSuccess |= $this->matchAttribute();

            if (!$stepSuccess) return false;
        }

        return $this->query;
    }

    /**
     * @param string $params
     */
    protected function prepareInitialData($params)
    {
        /** @var Amasty_Shopby_Helper_Url $helper */
        $helper = Mage::helper('amshopby/url');
        $params = trim($helper->checkRemoveSuffix($params), '/');

        $optionChar = Mage::getStoreConfig('amshopby/seo/option_char');
        if ($optionChar == '--') {
            $params = str_replace($optionChar, '@', $params);
            $optionChar = '@';
        }
        $this->params = $params;
        $this->optionChar = $optionChar;
        $this->query = array();
    }

    protected function matchPrice()
    {
        $ocq = preg_quote($this->optionChar, '/');
        $pattern = '/^price'.$ocq.'(\d+\.?\d*)-(\d*\.?\d*)/'; // 'price-10-20', 'price-20-'
        $success = preg_match($pattern, $this->params, $matches);
        if ($success)
        {
            $this->query['price'] = substr($matches[0], strlen('price' . $this->optionChar));
            $this->params = substr($this->params, strlen($matches[0] . $this->optionChar));
        }

        if (!$success) {
            $pattern = '/^price'.$ocq.'-(\d+\.?\d*)/'; // 'price--10'
            $success = preg_match($pattern, $this->params, $matches);
            if ($success)
            {
                $this->query['price'] = substr($matches[0], strlen('price' . $this->optionChar));
                $this->params = substr($this->params, strlen($matches[0] . $this->optionChar));
            }
        }

        return $success;
    }

    protected function matchDecimal()
    {
        $ocq = preg_quote($this->optionChar, '/');
        $pattern = '/^(\w[^'.$ocq.']+)'.$ocq.'(\d+\.?\d*[-,]\d+\.?\d*)/'; // 'length-10-20', 'length-2,10
        $success = preg_match($pattern, $this->params, $matches);
        if ($success)
        {
            /** @var Amasty_Shopby_Helper_Url $helper */
            $helper = Mage::helper('amshopby/url');
            $code = $helper->_convertAttributeToMagento($matches[1]);

            if ($helper->isDecimal($code)) {
                $value = $matches[2];
                $this->query[$code] = $value;
                $this->params = substr($this->params, strlen($matches[0] . $this->optionChar));
                return true;
            }
        }
        return false;
    }

    protected function matchAttribute()
    {
        $ocq = preg_quote($this->optionChar, '/');
        $pattern = '/^([^'.$ocq.']+)/'; // 'red'
        $success = preg_match($pattern, $this->params, $matches);
        if ($success)
        {
            $key = $matches[1];

            /** @var Amasty_Shopby_Helper_Url $helper */
            $helper = Mage::helper('amshopby/url');
            foreach ($helper->getAllFilterableOptionsAsHash() as $urlCode => $values){
                if (isset($values[$key])){
                    $code = $helper->_convertAttributeToMagento($urlCode);

                    $value = $values[$key];
                    if (isset($this->query[$code])) {
                        $this->query[$code].= ',' . $value;
                    } else {
                        $this->query[$code] = $value;
                    }
                    $this->params = substr($this->params, strlen($matches[0] . $this->optionChar));
                    return true;
                }
            }
        }
        return false;
    }
}
