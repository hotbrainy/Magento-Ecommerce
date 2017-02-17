<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
 
class MageWorx_CustomerCredit_Block_Customer_View_Credit extends MageWorx_CustomerCredit_Block_Customer_View_Abstract 
{
    public function getCredit() {
        return Mage::helper('mageworx_customercredit')->getCreditValue($this->getCustomer());
    }

    public function getCustomer() {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getEnableExpiration() {
        return Mage::getModel('mageworx_customercredit/credit', $this->getCustomer())->getEnableExpiration();
    }

    public function getFormAction() {
        return $this->getUrl('customercredit/index/refill',array('_secure'=>true));
    }

    public function getCustomerCreditCode() {
        $code = Mage::registry('customercredit_code');
        if (!empty($code))
            return $code->getCustomercreditCode();
        return '';
    }

    public function isEnabledCodes() {
        return Mage::helper('mageworx_customercredit')->isEnabledCodes();
    }
    public function getLeftDays() {
        return Mage::helper('mageworx_customercredit')->getCreditExpired($this->getCustomer(), Mage::app()->getStore()->getWebsiteId());
    }
    
    /**
     * Retrieve url for add product to cart
     *
     * @return string
     */
    public function getAddToCartFormUrl()
    {
        $magentoVersion = Mage::getVersion();
        $creditProduct = Mage::helper('mageworx_customercredit')->getCreditProduct(true);
        if (version_compare($magentoVersion, '1.8', '>=')) {
            return $this->getSubmitUrl($creditProduct);
        }
        return Mage::helper('checkout/cart')->getAddUrl($creditProduct);
    }
    
    /**
     * Checks if Add to Cart section is available
     *
     * @return bool
     */
    public function isAllowAddToCart()
    {
        $creditProduct = Mage::helper('mageworx_customercredit')->getCreditProduct(true);
        return $creditProduct && $creditProduct->isSalable();
    }
    
}