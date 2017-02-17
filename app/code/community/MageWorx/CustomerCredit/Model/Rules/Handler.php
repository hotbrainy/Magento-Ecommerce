<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Rules_Handler
{
    protected $_customer;
    protected $_order;
    protected $_object;
    protected $_ruleQty;

    public function setCustomer($customer) {
        $this->_customer = $customer;
    }

    public function setOrder($order) {
        $this->_order = $order;
    }

    public function setObject($object) {
        $this->_object = $object;
    }

    public function execute($rule) {
        if ($this->_checkConditions($rule)) {
            $rule = $this->_calculateCredit($rule);
            $this->_sendLog($rule);
        }
        return true;
    }

    /**
     * Check rule conditions
     * @param MageWorx_CustomerCredit_Model_Rules $rule
     * @return boolean
     */
    private function _checkConditions($rule) {
        $conditions = unserialize($rule['conditions_serialized']);
        $actionTag = MageWorx_CustomerCredit_Model_Rules_Customer_Action::MAGEWORX_CUSTOMER_ORDER_COMPLETE;
        $model = Mage::getModel('mageworx_customercredit/rules_customer_action');
        $collection = $model->getCollection();
        if(isset($conditions) && isset($conditions['conditions'])) {
            foreach ($conditions['conditions'] as $key => $condition) {
                $log = Mage::getModel('mageworx_customercredit/rules_customer_log');
                $success[$key] = true;
                $skipUrl = false;
                switch ($condition['attribute']) {
                    case 'registration':
                        if(!$this->_customer) return false;
                        $actionTag = MageWorx_CustomerCredit_Model_Rules_Customer_Action::MAGEWORX_CUSTOMER_ACTION_REGISTRATION;
                        $logCollectionModel = $log->getCollection()->setActionTag($actionTag);
                        $logCollection = $logCollectionModel->loadByRuleAndCustomer($rule['rule_id'], $this->_customer->getId());
                        $createdAt = $this->_customer->getCreatedAt(0);
                        $createdAt = str_replace('T',' ',$createdAt);
                        $regArr = explode(' ', $createdAt, 2);
                        $regDate = explode('-', $regArr[0], 3);
                        $regTimestamp = mktime(0, 0, 0, $regDate[1], $regDate[2], $regDate[0]);

                        $ruleRegDate = explode('-', $condition['value'], 3);
                        $ruleRegTimestamp = mktime(0, 0, 0, $ruleRegDate[1], $ruleRegDate[2], $ruleRegDate[0]);

                        if (!version_compare($regTimestamp, $ruleRegTimestamp, $condition['operator'])){
                            $success[$key] = false;
                        }
                        if($logCollection->getSize()) {
                            $success[$key] = false;
                            $skipUrl = true;
                            break;
                        }
                        break;
                    case 'number_of_orders':
                        if (!$this->_order) {
                            return false;
                        }
                        //if ($this->_order->getStatus() != 'complete') {
                        //    return false;
                        //}
                        $actionTag = MageWorx_CustomerCredit_Model_Rules_Customer_Action::MAGEWORX_CUSTOMER_ACTION_PLACEORDER;
                        $logCollectionModel = $log->getCollection()->setActionTag($actionTag);
                        $orders = Mage::getResourceModel('sales/order_collection');
                        $orders->getSelect()->where('customer_id=?',$this->_customer->getId());
                        $orders->load();

                        $items = $orders->getItems();
                        $collectionSize = count($items);
                        if (version_compare($collectionSize, $condition['value'], $condition['operator'])) {
                            $success[$key] = true;
                        } else {
                            $success[$key] = false;
                        }
                        break;
                    case 'order_total':
                        if(!$this->_order) {
                            return false;
                        }
                        //if ($this->_order->getStatus() != 'complete') {
                        //    return false;
                        //}
                        $actionTag = MageWorx_CustomerCredit_Model_Rules_Customer_Action::MAGEWORX_CUSTOMER_ACTION_PLACEORDER;
                        $logCollectionModel = $log->getCollection()->setActionTag($actionTag);

                        if (version_compare($this->_order->getGrandTotal(), $condition['value'], $condition['operator'])) {
                            $success[$key] = true;
                        } else {
                            $success[$key] = false;
                        }

                        break;
                    case 'total_amount':
                        if(!$this->_order) return false;
                        //if($this->_order->getStatus() != 'complete') {
                        //    return false;
                        //}
                        $actionTag = MageWorx_CustomerCredit_Model_Rules_Customer_Action::MAGEWORX_CUSTOMER_ACTION_PLACEORDER;
                        $logCollectionModel = $log->getCollection()->setActionTag($actionTag);
                        $orders = Mage::getResourceModel('sales/order_collection');
                        $orders->getSelect()
                            ->reset(Zend_Db_Select::WHERE)
                            ->columns(array('grand_subtotal' => 'SUM(subtotal)'))
                            ->where('customer_id='.$this->_customer->getId())
                            ->group('customer_id');
                        $data = $orders->getData();
                        #Depricated 2.6.0
                        /**
                        if (count($data) != 1){
                        $success[$key] = false;
                        }
                         */
                        if (!version_compare($data[0]['grand_subtotal'], $condition['value'], $condition['operator'])){
                            $success[$key] = false;
                        }
                        break;
                    case 'place_order':
                        if(!$this->_order) return false;
                        $actionTag = MageWorx_CustomerCredit_Model_Rules_Customer_Action::MAGEWORX_CUSTOMER_ACTION_PLACEORDER;
                        $logCollectionModel = $log->getCollection()->setActionTag($actionTag);
                        if (isset($rule['is_onetime'])) $isOnetime = $rule['is_onetime']; else $isOnetime = 1;
                        if($condition['value']==1) {
                            $logCollection = $logCollectionModel->loadByRuleAndCustomer($rule['rule_id'], $this->_customer->getId());
                            $logCollection->getSelect()->order('id ASC');
                            if ($logCollection->getSize() && $isOnetime) {
                                $lastItem = $logCollection->getLastItem();
                                $success[$key] = false;
                                $skipUrl = true;
                                break;
                            }
                            $success[$key] = true;
                        }
                        break;
                    default :
                        // product atributes:
                        $success[$key] = false;
                        $actionTag = MageWorx_CustomerCredit_Model_Rules_Customer_Action::MAGEWORX_CUSTOMER_ORDER_COMPLETE;
                        if($this->_order && ($this->_order->getStatus()=='complete')) {
                            $products = $this->_order->getAllItems();
                            $store = Mage::app()->getStore($this->_order->getStoreId());
                            $websiteId = $store->getWebsiteId();
                            $log->setWebsiteId($websiteId);
                            $this->_customer = Mage::getModel('customer/customer')->load($this->_order->getCustomerId());
                            $conditionProductModel = Mage::getModel($condition['type'])->loadArray($condition);
                            $conditionProductModel->getValueParsed();
                            $quoteId = $this->_order->getQuoteId();
                            $quote = Mage::getModel('sales/quote')->load($quoteId);

                            foreach($products as $item) {
                                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                                $product->setQuote($quote);
                                if ($conditionProductModel->validate($product)) {
                                    $success[$key] = true;
                                    $this->_ruleQty += $item->getQtyOrdered() - $item->getQtyRefunded() - $item->getQtyCanceled();
                                    //    break;
                                }
                            }
                        }
                }

                $result = $this->_checkAggregator($conditions,$success);
                if($condition['attribute']!=='review_product') {
                    if(!$result) return false;
                }
                if(!$skipUrl) {
                    $log->setId(null)
                        ->setRuleId($rule['rule_id'])
                        ->setCustomerId($this->_customer->getId())
                        ->setActionTag($actionTag)
                        ->setValue(time())
                        ->save();
                } else {
                    return false;
                }
                return true;
            }
        }
    }

    /**
     * Check rule aggregator
     * @param array $conditions
     * @param arary $success
     * @return boolean
     */
    private function _checkAggregator($conditions,$success) {
        $result = true;
        switch ($conditions['aggregator']){
            case 'any':
                switch ($conditions['value']){
                    case '1':
                        if(!in_array(true, $success)){
                            $result = false;
                        }
                        break;
                    case '0':
                        if (!in_array(false, $success)){
                            $result = false;
                        }
                        break;
                }
                break;
            case 'all':
                switch ($conditions['value']){
                    case 1:
                        if (in_array(false, $success)){
                            $result = false;
                        }
                        break;
                    case 0:
                        if (in_array(true, $success)){
                            $result = false;
                        }
                        break;
                }
                break;
        }
        return $result;
    }

    /**
     * Calculate credit value
     * @param MageWorx_CustomerCredit_Model_Rules $rule
     * @return MageWorx_CustomerCredit_Model_Rules
     */
    protected function _calculateCredit($rule) {
        // if qty dependent
        if (isset($rule['qty_dependent']) && ($rule['qty_dependent']==1)) {
            $rule['credit'] = $rule['credit'] * $this->_ruleQty;
        }
        if(isset($this->_order) && (strpos($rule['credit'],'%')!==false)) {

            $rule['credit'] = (int) str_replace('%', '', $rule['credit']);
            $total = (float)$this->_order->getGrandTotal();
            if(!$total) {
                $total = $this->_order->getSubtotalInvoiced();
            }
            $rule['credit'] = round($total*$rule['credit']/100,2);
        }

        return $rule;
    }

    /**
     * Send Credit Log to History
     * @param MageWorx_CustomerCredit_Model_Rules $rule
     * @return boolean
     */
    private function _sendLog($rule) {
        $creditChange = 0;
        $store = Mage::app()->getStore();
        if($this->_object) {
            $store = Mage::app()->getStore($this->_object->getStoreId());
        }
        if($this->_object && !$store) {
            $store = Mage::app()->getStore($this->_object->getFirstStoreId());
        }
        if($this->_order) {
            $store = Mage::app()->getStore($this->_order->getStoreId());
        }
        $websiteId = $store->getWebsiteId();

        if (isset($rule['is_onetime'])) $isOnetime = $rule['is_onetime']; else $isOnetime = 1;

        $rulesCustomer = Mage::getModel('mageworx_customercredit/rules_customer')->loadByRuleAndCustomer($rule['rule_id'], $this->_customer->getId());

        if (!$rulesCustomer || !$rulesCustomer->getId()) {
            $rulesCustomer = Mage::getModel('mageworx_customercredit/rules_customer')->setRuleId($rule['rule_id'])->setCustomerId($this->_customer->getId())->save();
        } else {
            if ($isOnetime) return;
        }

        if($this->_order) {
            $action = MageWorx_CustomerCredit_Model_Credit_Log::ACTION_TYPE_CREDITRULE;
            $order = $this->_order;
            $creditLog = Mage::getModel('mageworx_customercredit/credit_log')->loadByOrderAndAction($this->_order->getId(), $action, $rulesCustomer->getId());
            if (!$creditLog || !$creditLog->getId()) {
                $creditChange = $rule['credit'];
                if(strpos($creditChange,"%")!==false) {
                    $creditChange = str_replace("%", "",$creditChange);
                    $total = 0;
                    $total = $this->_order->getGrandTotal();
                    if($total == 0) {
                        $total = $this->_order->getSubtotalInclTax() + $this->_order->getShippingInclTax();
                    }
                    $creditChange = $total * $creditChange / 100;
                }

            }
        } else {
            $action = MageWorx_CustomerCredit_Model_Credit_Log::ACTION_TYPE_CREDIT_ACTION;
            $creditLog = Mage::getModel('mageworx_customercredit/credit_log');
            $order = NULL;
            $creditChange = (float)$rule['credit'];
        }
        Mage::getModel('mageworx_customercredit/credit', $this->_customer)->
            processRule($creditChange, $order, $rule, $rulesCustomer->getId(), $action);
    }
}