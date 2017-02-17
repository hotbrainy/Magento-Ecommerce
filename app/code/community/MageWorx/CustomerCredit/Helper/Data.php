<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_isShowCustomerCreditFlag = null;

    /**
     * Is customer credit enabled
     * @return boolean
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag('mageworx_customercredit/main/enable_credit');
    }

    /**
     * Is credit share enabled
     * @return boolean
     */
    public function isEnableShareCredit()
    {
        if (Mage::getStoreConfigFlag('mageworx_customercredit/main/enable_sharing')) {
            return $this->isShowCustomerCredit();
        }
        return false;
    }

    /**
     * Check scope
     * @return boolean
     */
    public function isScopePerWebsite()
    {
        return Mage::getStoreConfigFlag('mageworx_customercredit/main/credit_scope');
    }

    /**
     * DEPRICATED
     * @return boolean
     */
    public function isHideCreditUntilFirstUse()
    {
        return Mage::getStoreConfigFlag('mageworx_customercredit/main/hide_credit_until_first_use');
    }

    /**
     * Is credit code enabled
     * @return boolean
     */
    public function isEnabledCodes()
    {
        return Mage::getStoreConfigFlag('mageworx_customercredit/recharge_codes/enable_recharge_codes');
    }

    /**
     * Is autoinvoce
     * @return boolean
     */
    public function isEnabledInvoiceOrder()
    {
        return Mage::getStoreConfig('mageworx_customercredit/main/enable_invoice_order');
    }

    /**
     * Is enable partitial payment
     * @return boolean
     */
    public function isEnabledPartialPayment()
    {
        return Mage::getStoreConfigFlag('mageworx_customercredit/main/enable_partial_credit_payment');
    }

    /**
     * Can return creditmemo to credits
     * @return boolean
     */
    public function isCreditMemoShowCustomerCreditEnabled()
    {
        if (Mage::getStoreConfigFlag('mageworx_customercredit/main/enable_credit_memo_return')) {
            return $this->isShowCustomerCredit();
        }
        return false;
    }

    public function isEnabledCreditMemoReturn()
    {
        return Mage::getStoreConfigFlag('mageworx_customercredit/main/enable_credit_memo_return');
    }

    /**
     * Display the balance changing for the user due to the order editing
     * @return bool
     */
    public function displayBalanceChangingWhenOrderEdited()
    {
        return Mage::getStoreConfigFlag('mageworx_customercredit/main/order_edit_affect_credit_log');
    }

    /**
     * Display credit block in cart
     * @return boolean
     */
    public function isDisplayCreditBlockAtCart()
    {
        if (Mage::getStoreConfigFlag('mageworx_customercredit/main/display_credit_block_at_cart')) {
            return $this->isShowCustomerCredit();
        }
        return false;
    }

    /**
     * Is added credit column in order grid
     * @return boolean
     */
    public function isEnabledCreditColumnsInGridOrderViewTabs()
    {
        return Mage::getStoreConfigFlag('mageworx_customercredit/main/enable_credit_columns_in_grid_order_view_tabs');
    }

    /**
     * Is added credit column in customer grid
     * @return boolean
     */
    public function isEnabledCustomerBalanceGridColumn()
    {
        return Mage::getStoreConfigFlag('mageworx_customercredit/main/enable_customer_balance_grid_column');
    }

    /**
     * Get totals
     * @return array
     */
    public function getCreditTotals()
    {
        return explode(',', Mage::getStoreConfig('mageworx_customercredit/main/credit_totals'));
    }

    /**
     * Get default QTY for credit product
     * @return int
     */
    public function getDefaultQtyCreditUnits()
    {
        return Mage::getStoreConfig('mageworx_customercredit/main/default_qty_credit_units');
    }

    /**
     * Get credti product sku
     * @return string
     */
    public function getCreditProductSku()
    {
        return Mage::getStoreConfig('mageworx_customercredit/main/credit_product');
    }

    /**
     * Is Enable Custom Value
     * @return string
     */
    public function isEnabledCustomValue()
    {
        return Mage::getStoreConfig('mageworx_customercredit/main/enable_custom_value');
    }

    /**
     * DEPRICATED
     * @return json
     */
    public function getJsCurrency()
    {
        $websiteCollection = Mage::getSingleton('adminhtml/system_store')->getWebsiteCollection();
        $currencyList = array();
        foreach ($websiteCollection as $website) {
            $currencyList[$website->getId()] = $website->getBaseCurrencyCode();
        }
        return Zend_Json::encode($currencyList);
    }

    /**
     * Get sales address
     * @param Mage_Sales_Model_Quote $quote
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getSalesAddress($quote)
    {
        $address = $quote->getShippingAddress();
        if ($quote->isVirtual()) {
            $address = $quote->getBillingAddress();
        }
        return $address;
    }

    /**
     * Check apply credits and return array of values
     * @param Mage_Sales_Model_Quote $quote
     * @param int $customerId
     * @param int $websiteId
     * @return array
     */
    public function checkApplyCreditsSum($quote, $customerId, $websiteId)
    {
        //   return true;
        $result = array();
        $store = Mage::app()->getStore();
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $customerGroupId = $customer->getGroupId();
        $websiteId = $store->getWebsiteId();
        $ruleModel = Mage::getResourceModel('mageworx_customercredit/rules_collection');
        $ruleModel->setValidationFilter($websiteId, $customerGroupId)->setRuleTypeFilter(MageWorx_CustomerCredit_Model_Rules::CC_RULE_TYPE_APPLY);
        foreach ($ruleModel->getData() as $rule) {
            if (!count($result)) {
                $result = array(0);
            }
            $conditions = unserialize($rule['conditions_serialized']);
            if ($conditions) {
                $conditionModel = Mage::getModel('mageworx_customercredit/rules_condition_combine')->setPrefix('conditions')->loadArray($conditions);
                $_result = $conditionModel->validate($this->getSalesAddress($quote), TRUE);
                if (is_array($_result)) {
                    $result = array_merge($result, $_result);
                }
            }
        }
        return $result;
    }

    public function getAllBillingAddresses($quote)
    {
        $addresses = array();
        foreach ($quote->getAddressesCollection() as $address) {
            if ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING
                && !$address->isDeleted()
            ) {
                $addresses[] = $address;
            }
        }
        return $addresses;
    }

    /**
     * Get partitial payment type
     * @param Mage_Sales_Model_Quote $quote
     * @param int $customerId
     * @param int $websiteId
     * @return boolean|int
     *  -3 - can't apply credits
     *  -2 - hide customer credit
     *  -1 - no balabce checkbox
     *  0 - no balance radio
     *  1 - checkbox (partial payment)
     *  2 - radio (full payment)
     */
    public function isPartialPayment($quote, $customerId = null, $websiteId = null)
    {
        if (!$this->isShowCustomerCredit()) {
            return -2;
        }
        if (!$quote) {
            return -2;
        }

        if (Mage::app()->getStore()->isAdmin()) {
            $customerId = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();
        }

        if (!$customerId) {
			return false;
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if (!$customer->isEmpty()) {
                $customerId = $customer->geId();
            } else {
                return false;
            }
        }
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $value = $this->getCreditValue($customer);
        $value = $this->getValueExchangeRateDivided($value);
        $isEnabledPartialPayment = $this->isEnabledPartialPayment();
        if ($value == 0) {
            if ($isEnabledPartialPayment) return -1; else return 0;
        }

        // check apply credits
        $productConditionsPrice = $this->checkApplyCreditsSum($quote, $customerId, $websiteId);
        if (sizeof($productConditionsPrice) > 0 && !array_sum($productConditionsPrice)) {
            return -3;
        }

        if (Mage::getSingleton('customer/session')->getData('customer_credit_rule')) {
            return 1;
        }
        if (Mage::app()->getStore()->isAdmin()) {
            $allItems = $quote->getAllItems();
            $productIds = array();
            foreach ($allItems as $item) {
                $productIds[] = $item->getProductId();
            }
        } else {
            $productIds = Mage::getSingleton('checkout/cart')->getProductIds();
        }

        $addressType = Mage_Sales_Model_Quote_Address::TYPE_BILLING;
        $creditProductSku = $this->getCreditProductSku();
        foreach ($productIds as $productId) {
            $product = Mage::getModel('catalog/product')->load($productId);
            if (!$product) continue;
            // is credit product - no credit!
            if ($creditProductSku && $product->getSku() == $creditProductSku) return 0;

            $productTypeId = $product->getTypeId();
            if ($productTypeId != 'downloadable' && !$product->isVirtual()) {
                $addressType = Mage_Sales_Model_Quote_Address::TYPE_SHIPPING;
                break;
            }
        }

        //shipping or billing
        if ($addressType == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING) {
            $addresses = $quote->getAllShippingAddresses();
        } else {
            $addresses = $this->getAllBillingAddresses($quote);
        }

        $subtotal = 0;
        $shipping = 0;
        $tax = 0;
        $grandTotal = 0;
        $tail = 0;
        foreach ($addresses as $address) {
            ///////
            $subtotal = floatval($address->getBaseSubtotalWithDiscount() - $address->getMwRewardpointDiscount()); //$address->getBaseSubtotal();
            $shipping = floatval($address->getBaseShippingAmount() - $address->getBaseShippingTaxAmount());
            $tax = floatval($address->getBaseTaxAmount());

            $grandTotal = floatval($quote->getBaseGrandTotal() + $address->getBaseCustomerCreditAmount());
            if ($grandTotal == 0) $grandTotal = floatval(array_sum($address->getAllBaseTotalAmounts()));
            if ($grandTotal == 0) $grandTotal = $subtotal + $shipping + $tax;
//            echo $subtotal.'|'.$shipping.'|'.$tax.'|='.$grandTotal.'<br/>';        
            $tail = $grandTotal;
            ///////
        }
        $creditTotals = $this->getCreditTotals();
        if (count($creditTotals) < 3) {
            $amount = 0;
            foreach ($creditTotals as $field) {
                switch ($field) {
                    case 'subtotal':
                        $amount += $subtotal;
                        $tail -= $subtotal;
                        break;
                    case 'shipping':
                        $amount += $shipping;
                        $tail -= $shipping;
                        break;
                    case 'tax':
                        $amount += $tax;
                        $tail -= $tax;
                        break;
                    case 'fees':
                        $baseCreditLeft += $address->getBaseMultifeesAmount();
                        $creditLeft += $address->getMultifeesAmount();
                        break;
                }
            }
        } else {
            $amount = $grandTotal;
            $tail = 0;
        }

        if (sizeof($productConditionsPrice) > 0) {
            $sum = array_sum($productConditionsPrice);
            $baseCreditLeft = $sum;
            $creditLeft = $sum;
        }

        $amount = round($amount, 2);
        $tail = round($tail, 2);
//        echo $amount.'|'.$tail.'|'.$value; //exit;

        if ($value >= $amount && $tail == 0) {
            $maxCredit = $this->getMinOrderAmount();
            if ($maxCredit && ($value > ($amount * $maxCredit / 100))) {

                if ($isEnabledPartialPayment) return 1; else return 0;
            }
            return 2;
        } else {
            if ($isEnabledPartialPayment) return 1; else return 0;
        }
    }

    public function getMinOrderAmount()
    {
        return Mage::getStoreConfig('mageworx_customercredit/main/min_order_amount');
    }


    /**
     * Create credit product
     * @return boolean
     */
    public function createCreditProduct()
    {
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();

        $attributeSetId = $connection->fetchOne("SELECT `default_attribute_set_id` FROM `" . $tablePrefix . "eav_entity_type` WHERE `entity_type_code` = 'catalog_product'");
        if (!$attributeSetId) return false;

        $productData = array(
            'store_id' => 0,
            'attribute_set_id' => $attributeSetId,
            'type_id' => 'virtual',
            '_edit_mode' => 1,
            'name' => 'Credit Units',
            'sku' => 'customercredit',
            'website_ids' => array_keys(Mage::app()->getWebsites()),
            'status' => 1,
            'tax_class_id' => 0,
            'url_key' => '',
            'visibility' => 1,
            'news_from_date' => '',
            'news_to_date' => '',
            'is_imported' => 0,
            'price' => 1,
            'cost' => '',
            'special_price' => '',
            'special_from_date' => '',
            'special_to_date' => '',
            'enable_googlecheckout' => 1,
            'meta_title' => '',
            'meta_keyword' => '',
            'meta_description' => '',
            'thumbnail' => 'no_selection',
            'small_image' => 'no_selection',
            'image' => 'no_selection',
            'media_gallery' => Array(
                'images' => '[]',
                'values' => '{"thumbnail":null,"small_image":null,"image":null}'
            ),
            'description' => 'This product is used to purchase credit units to fulfill internal balance.',
            'short_description' => 'This product is used to purchase credit units to fulfill internal balance.',
            'custom_design' => '',
            'custom_design_from' => '',
            'custom_design_to' => '',
            'custom_layout_update' => '',
            'options_container' => 'container2',
            'page_layout' => '',
            'is_recurring' => 0,
            'recurring_profile' => '',
            'use_config_gift_message_available' => 1,
            'stock_data' => Array(
                'manage_stock' => 0,
                'original_inventory_qty' => 0,
                'qty' => 0,
                'use_config_min_qty' => 1,
                'use_config_min_sale_qty' => 1,
                'use_config_max_sale_qty' => 1,
                'is_qty_decimal' => 0,
                'use_config_backorders' => 1,
                'use_config_notify_stock_qty' => 1,
                'use_config_enable_qty_increments' => 1,
                'use_config_qty_increments' => 1,
                'is_in_stock' => 0,
                'use_config_manage_stock' => 0
            ),
            'can_save_configurable_attributes' => false,
            'can_save_custom_options' => false,
            'can_save_bundle_selections' => false
        );

        try {
            $product = Mage::getModel('catalog/product')->setData($productData)->save();
            $productId = $product->getId();
            if (version_compare(Mage::getVersion(), '1.5.0', '>=')) {
                Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($productId);
            } else {
                Mage::getModel('catalogrule/rule')->applyToProduct($productId);
            }
            return $productId;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get credit product
     * @param boolean $fromConfig
     * @return boolean
     */
    public function getCreditProduct($fromConfig = false)
    {
        $sku = $this->getCreditProductSku();
        $productId = false;
        if (!$sku) {
            if ($fromConfig) return false;
            $sku = 'customercredit';
        }
        $storeId = Mage::app()->getStore()->getId();
        $productId = Mage::getModel('catalog/product')->setStoreId($storeId)->getIdBySku($sku);
        if (!$productId) return false;
        return Mage::getModel('catalog/product')->setStoreId($storeId)->load($productId);
    }

    /**
     * Get real credit value
     * @param obj $customer
     * @param int $websiteId
     * @return float
     */
    public function getRealCreditValue($customer = null)
    {
        if (!$customer) {
            $customer = $this->getCustomer();
        }
        $websiteId = $customer->getWebsiteId();
        if (!$websiteId) {
            $storeId = Mage::app()->getRequest()->getParam('store_id', Mage::app()->getStore()->getId());
            $store = Mage::app()->getStore($storeId);
            $websiteId = $store->getWebsiteId();
        }
        $credit = Mage::getModel('mageworx_customercredit/credit', $customer);
        $creditValue = floatval($credit->getValue());

        if (Mage::app()->getRequest()->getControllerName() == 'sales_order_edit' || Mage::app()->getRequest()->getControllerName() == 'orderspro_order_edit') {
            $orderId = Mage::getSingleton('adminhtml/session_quote')->getOrderId();
            $orderBaseCustomerCreditAmount = floatval(Mage::getModel('sales/order')->load($orderId)->getBaseCustomerCreditAmount());
            if ($orderBaseCustomerCreditAmount) {
                $creditValue += $orderBaseCustomerCreditAmount;
                Mage::getSingleton('adminhtml/session_quote')->setUseInternalCredit(true);
            }
        }

        return $creditValue;
    }

    /**
     * Get Credit value
     * @param obj $customer
     * @param int $websiteId
     * @return float
     */
    public function getCreditValue($customer)
    {
        if ($credit = Mage::getSingleton('customer/session')->getCustomCreditValue()) {
            return $credit;
        }

        $credit = $this->getRealCreditValue($customer);

        return $credit;
    }

    /**
     * Get Used Credit Value
     * @param obj $customer
     * @param string $websiteId
     * @return float
     */
    public function getUsedCreditValue($customer = null)
    {
        if (!Mage::getSingleton('adminhtml/session_quote')->getCustomer()->isEmpty()) {
            return $this->getCreditValue($customer ? $customer : Mage::getSingleton('adminhtml/session_quote')->getCustomer());
        } else {
            return $this->getCreditValue(Mage::getSingleton('customer/session')->getCustomer());
        }
    }

    /**
     * Get credit expired date
     * @param Mage_Customer_Model_Customer $customer
     * @return int
     */
    public function getCreditExpired($customer)
    {
        $today = strtotime(date("Y-m-d"));
        $date = $this->getExpirationTime($customer);

        if ($date == '0000-00-00') return false;

        $hash = (strtotime($date) - $today) / (3600 * 24);
        return $hash;
    }

    /**
     * @param $customer
     * @param $websiteId
     * @return string (unix time)
     */
    public function getExpirationTime($customer)
    {
        $credit = Mage::getModel('mageworx_customercredit/credit', $customer);
        if (!Mage::app()->getStore()->isAdmin()) {
            $credit->setWebsiteId($customer->getWebsiteId());
        }
        $date = $credit->getExpirationTime();
        return $date;
    }

    /**
     * @param $customer
     * @param $websiteId
     * @return bool
     */
    public function getEnableExpiration($customer)
    {
        $credit = Mage::getModel('mageworx_customercredit/credit', $customer);
        if (!Mage::app()->getStore()->isAdmin()) {
            $credit->setWebsiteId($customer->getWebsiteId());
        }
        $expiration = $credit->getEnableExpiration();
        return $expiration;
    }

    /**
     * DEPRICATED
     * @param object $customer
     * @return boolean
     */
    public function checkFirstUseCustomerCredit($customer)
    {
        $creditValue = Mage::getModel('mageworx_customercredit/credit', $customer)
            ->getData('value');
        if (!(int)$creditValue) return false;
        return true;
    }

    /**
     * Is can show credits
     * @return boolean
     */
    public function isShowCustomerCredit()
    {
        if (!is_null($this->_isShowCustomerCreditFlag)) return $this->_isShowCustomerCreditFlag;
        if ($this->isEnabled()) {

            $customer = null;
            if (Mage::app()->getStore()->isAdmin()) {
                $customer = $this->getCustomer();
                $customerGroupId = $customer->getGroupId();
            } else {
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $customerGroupId = $customer->getGroupId();
            }
            $avalibleGroupIds = explode(',', $this->getCustomerGroups());
            if (in_array($customerGroupId, $avalibleGroupIds)) {
                if (Mage::app()->getStore()->isAdmin()) {
                    $this->_isShowCustomerCreditFlag = true;
                    return true;
                }
                if (!$this->isHideCreditUntilFirstUse() || $this->checkFirstUseCustomerCredit($customer)) {
                    $this->_isShowCustomerCreditFlag = true;
                    return true;
                } else {
                    $this->_isShowCustomerCreditFlag = false;
                    return false;
                }
            }
        }
        $this->_isShowCustomerCreditFlag = false;
        return false;
    }

    /**
     * @return array
     */
    public function getCustomerGroups()
    {
        return Mage::getStoreConfig('mageworx_customercredit/main/customer_group');
    }

    /**
     * @return string
     */
    public function getDefaultExpirationPeriod()
    {
        return Mage::getStoreConfig('mageworx_customercredit/expiration/default_expiration_period');
    }

    /**
     * @return boolean
     */
    public function isEnabledUpdateExpirationDate()
    {
        return Mage::getStoreConfig('mageworx_customercredit/expiration/update_expiration_date');
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    protected function getCustomer()
    {
        $customer = Mage::getModel('customer/customer');
        $customerSession = Mage::getSingleton('customer/session');
        if ($customerSession->isLoggedIn()) {
            return $customerSession->getCustomer();
        }
        if (Mage::registry('current_customer')) {
            return Mage::registry('current_customer');
        }

        //create order
        if ($customer->isEmpty()) {
            $customerId = Mage::getSingleton('adminhtml/session_quote')->getCustomerId();
            $customer = Mage::getModel('customer/customer')->load($customerId);
        }

        //refund order
        $orderId = (int)Mage::app()->getRequest()->getParam('order_id');
        if ($customer->isEmpty() && $orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $customerId = $order->getCustomerId();
            $customer = Mage::getModel('customer/customer')->load($customerId);
        }
        return $customer;
    }

    /**
     * Get Exchange Rate Setting
     * @return string
     */
    public function getExchangeRate()
    {
        return Mage::getStoreConfig('mageworx_customercredit/main/exchange_rate');
    }

    /**
     * @param $value
     * @return float
     */
    public function getValueExchangeRateDivided($value)
    {
        return (float)($value / $this->getExchangeRate());
    }

    /**
     * @param $value
     * @return float
     */
    public function getValueExchangeRateMultiplied($value)
    {
        return (float)($value * $this->getExchangeRate());
    }

    /**
     * Get Days Left Setting
     * @return string
     */
    public function getNotifyExpirationDateLeft()
    {
        return Mage::getStoreConfig('mageworx_customercredit/expiration/notify_expiration_date_left');
    }

    /**
     * Get Length of recharge code
     * @return string
     */
    public function getCodeLength()
    {
        return Mage::getStoreConfig('mageworx_customercredit/recharge_codes/code_length');
    }

    /**
     * Get Lenght of group for recharge code
     * @return string
     */
    public function getGroupLength()
    {
        return Mage::getStoreConfig('mageworx_customercredit/recharge_codes/group_length');
    }

    /**
     * Get Separator for group
     * @return string
     */
    public function getGroupSeparator()
    {
        return Mage::getStoreConfig('mageworx_customercredit/recharge_codes/group_separator');
    }

    /**
     * Get recharge code format
     * @return string
     */
    public function getCodeFormat()
    {
        return Mage::getStoreConfig('mageworx_customercredit/recharge_codes/code_format');
    }

    /**
     * Get + or - to number
     * @param string
     * @return string
     */
    public function getAddedOrDeductedValue($value) {
        $value = round($value,2);
        $sign = "";

        if ($value > 0) {
            $sign = "+";
        } elseif ($value < 0) {
            $sign = "-";
        }
        return $sign . abs($value);
    }

    /**
     * @return string
     */
    public function getProductReview()
    {
        return Mage::getStoreConfig('mageworx_customercredit/give_credit/product_review');
    }

    /**
     * @return string
     */
    public function getCustomerBirthday()
    {
        return Mage::getStoreConfig('mageworx_customercredit/give_credit/customer_birthday');
    }

    /**
     * @return string
     */
    public function getNewsletterSubscription()
    {
        return Mage::getStoreConfig('mageworx_customercredit/give_credit/newsletter_subscription');
    }

    /**
     * @return string
     */
    public function getProductTag()
    {
        return Mage::getStoreConfig('mageworx_customercredit/give_credit/product_tag');
    }

    /**
     * Get Expiration Enable Setting
     * @param $customerExpirationFlag
     * @return bool
     */
    public function isExpirationEnabled($customerExpirationFlag)
    {
        if ($customerExpirationFlag == 0) {
            return false;
        } else if ($customerExpirationFlag == 2) {
            if (Mage::getStoreConfig('mageworx_customercredit/expiration/expiration_enable')) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    /**
     * @return bool
     */
    public function isSendEmailTemplates() {
        return Mage::getStoreConfig('mageworx_customercredit/email_config/send_email_templates');
    }

    /**
     * @return bool
     */
    public function isTemplateActionEnabled($action) {
        return Mage::getStoreConfig('mageworx_customercredit/email_config/for_'.$action);
    }

    /**
     * @return string
     */
    public function getTemplate($action, $storeId) {
        return Mage::getStoreConfig('mageworx_customercredit/email_config/tpl_'.$action, $storeId);
    }

    /**
     * @return string
     */
    public function getBccEmails() {
        return Mage::getStoreConfig('mageworx_customercredit/email_config/enable_bcc');
    }
}