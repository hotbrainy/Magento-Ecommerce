<?php
/**
 * Created by PhpStorm.
 * User: riterrani
 * Date: 9/13/16
 * Time: 8:09 PM
 */ 
class Entangled_Custom_Helper_Data extends Mage_Core_Helper_Abstract {

    protected $_attributeValues = array();

    public function getTotalCustomerCredit(){
        $helper = Mage::helper('mageworx_customercredit');

        $quote = Mage::getSingleton('checkout/cart')->getQuote();

        $total = ($quote->getSubtotalWithDiscount()-$quote->getCustomerCreditAmount()) * $helper->getExchangeRate();

        return $total < 0 ? 0 : $total;
    }

    public function floor_dec($number,$precision = 2,$separator = '.') {
        $numberpart=explode($separator,$number);
        $numberpart[1]=substr_replace($numberpart[1],$separator,$precision,0);
        if($numberpart[0]>=0) {
            $numberpart[1]=substr(floor('1'.$numberpart[1]),1);
        } else {
            $numberpart[1]=substr(ceil('1'.$numberpart[1]),1);
        }
        $ceil_number= array($numberpart[0],$numberpart[1]);
        return implode($separator,$ceil_number);
    }

    public function isSubscriptionUser($includeCart = false){
        $customerSession = Mage::getSingleton("customer/session");
        if($includeCart && $this->isMembershipCouponInCart()){
            return true;
        }
        if($customerSession->isLoggedIn() && $customerSession->getCustomer()->getGroupId() == Entangled_Purchasediscount_Helper_Data::DISCOUNT_GROUP_ID){
            return true;
        }

        return false;
    }

    public function isMembershipCouponInCart(){
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        foreach($quote->getAllItems() as $item){
            if($item->getSku() == Entangled_Purchasediscount_Helper_Data::DISCOUNT_SKU){
                return true;
            }
        }
    }

    public function getLayerUrl($attribute,$value){
        $baseUrl = Mage::getBaseUrl()."books.html?";
        if(!isset($this->_attributeValues[$attribute])){
            $attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')->setCodeFilter($attribute)->getFirstItem();
            $attributeId = $attributeInfo->getAttributeId();
            $attributeModel = Mage::getModel('catalog/resource_eav_attribute')->load($attributeId);
            $this->_attributeValues[$attribute] = $attributeModel ->getSource()->getAllOptions(false);
        }
        foreach($this->_attributeValues[$attribute] as $attributeValue){
            if($attributeValue["label"] == $value){
                return $baseUrl.$attribute."=".$attributeValue["value"];
            }
        }

        return $baseUrl;
    }

    public function escapeHtml($string){
        $dom = new DOMDocument();

// Load with no html/body tags and do not add a default dtd
        $dom->loadHTML('<?xml encoding="utf-8" ?>' .$string, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $string = $dom->saveHTML();

        return $string;
    }

    public function _escapeHtml($string){

        $list = get_html_translation_table(HTML_ENTITIES);
        unset($list['"']);
        unset($list['<']);
        unset($list['>']);
        unset($list['&']);

        return strtr($string,$list);
    }

    public function getShortDesc($description){
        $shortDescriptionSpaceIndex = strpos($description, ' ', 260);
        $string = $this->escapeHtml(substr($description, 0,$shortDescriptionSpaceIndex  === false ? 260 : $shortDescriptionSpaceIndex) . "...");

        return $string;
    }


    public function isRepeatedProduct($product){
        $customerSession = Mage::getSingleton("customer/session");
        /** @var Mage_Sales_Model_Resource_order_Collection $collection */
        $collection = Mage::getModel('sales/order')->getCollection();
        $resource = $collection->getResource();
        $collection->getSelect()->join( array('order_item'=> $resource->getTable("order_item")), 'order_item.order_id = main_table.entity_id', array());
        $collection->addFieldToFilter("main_table.customer_id",$customerSession->getCustomerId());
        $collection->addFieldToFilter("order_item.sku",$product->getSku());
        $collection->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $collection->getSelect()->columns(new Zend_Db_Expr("1"));
        $collection->getSelect()->limit(1,0);

        return (bool)$collection->count();
    }
}