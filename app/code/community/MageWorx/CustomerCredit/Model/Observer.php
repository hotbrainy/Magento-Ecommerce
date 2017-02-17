<?php

/**
 * MageWorx
 * Loyalty Booster Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerCredit
 * @copyright  Copyright (c) 2016 MageWorx (http://www.mageworx.com/)
 */
class MageWorx_CustomerCredit_Model_Observer
{
    /**
     * Add credits to order in magento after 1.9
     * @param Varien_Event_Observer $observer
     */
    public function saveCreditsInOrder($observer) {
        $quote = $observer->getQuote();
        if (version_compare(Mage::getVersion(), '1.9', '<')) return ;
        foreach ($quote->getAllAddresses() as $address) {

            $baseCredit = $address->getBaseCustomerCreditAmount();
            $credit     = $address->getCustomerCreditAmount();
            $quote->setBaseCustomerCreditAmount($quote->getBaseCustomerCreditAmount() + $baseCredit);
            $quote->setCustomerCreditAmount($quote->getCustomerCreditAmount() + $credit);
        }
    }
    
    /**
     * Save Customer Credit Code After
     * @param Varien_Event_Observer $observer
     */
    public function saveCodeAfter(Varien_Event_Observer $observer) {
        $code = $observer->getEvent()->getCode();
        $code->getLogModel()
            ->setCodeModel($code)
            ->save();
    }

    /**
     * Save Customer Credit Value After
     * @param Varien_Event_Observer $observer
     */
    public function saveCreditAfter(Varien_Event_Observer $observer) {
        $credit = $observer->getEvent()->getCredit();

    }

    /**
     * Prepare Customer Credit Value when Customer Save
     * @param Varien_Event_Observer $observer
     */
    public function prepareCustomerSave(Varien_Event_Observer $observer) {
        $customer = $observer->getEvent()->getCustomer();
        $request  = $observer->getEvent()->getRequest();
        if ($data = $request->getPost('customercredit'))
        {
            $customer->setCustomerCreditData($data);
        }
    }

    /**
     * Save Customer Credit Value when Customer Save
     * @param Varien_Event_Observer $observer
     */
    public function saveCustomerAfter(Varien_Event_Observer $observer) {
        if (!Mage::helper('mageworx_customercredit')->isEnabled()) return false;
        $customer = $observer->getEvent()->getCustomer();
        $customerCredit = Mage::getModel('mageworx_customercredit/credit', $customer);
//        echo "<pre>"; print_r($customer->getData()); exit;
        if (($data = $customer->getCustomerCreditData()) && !empty($data['value_change'])) {
            // no minus
            if ((floatval($data['credit_value']) + floatval($data['value_change'])) < 0 ) $data['value_change'] = floatval($data['credit_value'])*-1;
        }
        $customerCredit->setValue($data['credit_value']);
        $customerCredit->setValueChange($data['value_change']);
        $customerCredit->save();

    }
    
    /**
     * Collect totals before
     * @param Varien_Event_Observer $observer
     */
    public function collectQuoteTotalsBefore(Varien_Event_Observer $observer) {
        $quote = $observer->getEvent()->getQuote();
        $quote->setCustomerCreditTotalsCollected(false);
    }

    /**
     * Place order Before
     * @param Varien_Event_Observer $observer
     */
    public function placeOrderBefore(Varien_Event_Observer $observer) {
        $helper = Mage::helper('mageworx_customercredit');
        if (!$helper->isEnabled()) return;

        $order = $observer->getEvent()->getOrder();
        /* @var $order Mage_Sales_Model_Order */
        if ($order->getBaseCustomerCreditAmount() > 0) {

            $credit = $helper->getCreditValue($order->getCustomer());
            $credit = $helper->getValueExchangeRateDivided($credit);
            if (($order->getBaseCustomerCreditAmount() - $credit) >= 0.0001) {
                Mage::getSingleton('checkout/type_onepage')
                    ->getCheckout()
                    ->setUpdateSection('payment-method')
                    ->setGotoSection('payment');
                Mage::throwException(Mage::helper('mageworx_customercredit')->__('Not enough Credit Amount to complete this Order.'));
            }
        }
    }
    
    /**
     * Check can using customer credit when order place
     * @param Varien_Event_Observer $observer
     * @return boolean
     */
    public function reduceCustomerCreditValue(Varien_Event_Observer $observer) {
        if (!Mage::helper('mageworx_customercredit')->isEnabled()) return false;
        $order = $observer->getEvent()->getOrder();
        $customer = $order->getCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customer);
        $needUseCreditMarker = Mage::registry('need_reduce_customercredit');
        /* @var $order Mage_Sales_Model_Order */
        if (($order->getBaseCustomerCreditAmount()>0) || $needUseCreditMarker) {
            //reduce credit value
            Mage::getModel('mageworx_customercredit/credit', $customer)->useCredit($order);
            return true;            
        }
        return false;
    }

    /**
     * Recalc Invoice
     * @param Varien_Event_Observer $observer
     */
    public function saveInvoiceAfter(Varien_Event_Observer $observer) {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();

        if ($invoice->getBaseCustomerCreditAmount()) {
            $order->setBaseCustomerCreditInvoiced($order->getBaseCustomerCreditInvoiced() + $invoice->getBaseCustomerCreditAmount());
            $order->setCustomerCreditInvoiced($order->getCustomerCreditInvoiced() + $invoice->getCustomerCreditAmount());
        }
    }

    /**
     * Check order status
     * @param Varien_Event_Observer $observer
     * @return MageWorx_CustomerCredit_Model_Observer
     */
    public function loadOrderAfter(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getOrder();

        if ($order->canUnhold()) {
            return $this;
        }

        if ($order->getState() === Mage_Sales_Model_Order::STATE_CANCELED ||
            $order->getState() === Mage_Sales_Model_Order::STATE_CLOSED ) {
            return $this;
        }


        if (abs($order->getCustomerCreditInvoiced() - $order->getCustomerCreditRefunded())<.0001) {
            return $this;
        }
        $order->setForcedCanCreditmemo(true);

        return $this;
    }
    
    /**
     * Check if need recalc refund
     * @param Varien_Event_Observer $observer
     */
    public function returnRefundData($observer) {
        $order = $observer->getOrder();
        if($value = Mage::registry('need_setnull_total_refunded')) {
            Mage::unregister('need_setnull_total_refunded');
            $order->setTotalRefunded($value)->save();
        }
    }

    /**
     * Create creditmemo
     * @param Varien_Event_Observer $observer
     * @return MageWorx_CustomerCredit_Model_Observer
     */
    public function refundCreditmemo(Varien_Event_Observer $observer) {                
        
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if(Mage::registry('cc_order_refund')) {
            return true;
        }
        Mage::register('cc_order_refund', true, true);
        $order = $creditmemo->getOrder();        

        // get real total
        $baseTotal = $creditmemo->getBaseGrandTotal();
        if ($order->getBaseCustomerCreditAmount()>$order->getBaseCustomerCreditRefunded()) {
            $baseTotal += $creditmemo->getBaseCustomerCreditAmount();
        }
        $baseTotal = floatval($baseTotal);
        
        // add message Returned credit amount..
        $post = Mage::app()->getRequest()->getParam('creditmemo');                        
        if (isset($post['credit_return'])) {
            $baseCreditAmountReturn = floatval($post['credit_return']);
            // validation
            if ($baseCreditAmountReturn>$baseTotal) {
                $baseCreditAmountReturn = $baseTotal;
            }
        } else {
            $baseCreditAmountReturn = $creditmemo->getBaseCustomerCreditAmount();
        }
        
        if ($baseCreditAmountReturn>0) {
            // set CustomerCreditRefunded
            $order->setBaseCustomerCreditRefunded($order->getBaseCustomerCreditRefunded() + $baseCreditAmountReturn);            
            $creditAmountReturn = $creditmemo->getStore()->convertPrice($baseCreditAmountReturn, false, false);
            $order->setCustomerCreditRefunded($order->getCustomerCreditRefunded() + $creditAmountReturn);                                  
            
            // if payment is not 100% credit
            if ($order->getBaseGrandTotal()!=0) {
                // set [base_]total_refunded 
                $order->setBaseTotalRefunded(($order->getBaseTotalRefunded() - $creditmemo->getBaseGrandTotal()) + ($baseTotal - $baseCreditAmountReturn));
                $total = $creditmemo->getStore()->convertPrice($baseTotal, false, false);

                $tmpTotalRefunded = ($order->getTotalRefunded() - $creditmemo->getGrandTotal()) + ($total - $creditAmountReturn);
                $b = $order->getTotalRefunded();
                if($order->getTotalRefunded()!=$tmpTotalRefunded) {
                    $order->setTotalRefunded($tmpTotalRefunded);
                    $a = $order->getTotalRefunded();
                    if($order->getTotalRefunded()+$creditAmountReturn-$total < .0001) {
                        Mage::register('need_setnull_total_refunded',$order->getTotalRefunded(),TRUE);
                        $order->setTotalRefunded($order->getTotalPaid());
                    }
                }
                
                
            }
            
            if (abs($order->getCustomerCreditInvoiced() - $order->getCustomerCreditRefunded())<.0001) {
                $order->unsForcedCanCreditmemo();
                 Mage::register('can_closed_order', true, true);
            }
            
           
            // set message
            $payment = $order->getPayment();
            

            if ($order->getBaseGrandTotal()!=0) {
                if ($creditmemo->getDoTransaction() && $creditmemo->getInvoice()) {
                    // online
                    $message = Mage::helper('mageworx_customercredit')->__('Refunded amount of %s online.', $payment->getOrder()->getBaseCurrency()->formatTxt($baseTotal - $baseCreditAmountReturn))."<br/>";
                } else {
                    // offline
                    $message = Mage::helper('mageworx_customercredit')->__('Refunded amount of %s offline.', $payment->getOrder()->getBaseCurrency()->formatTxt($baseTotal - $baseCreditAmountReturn))."<br/>";
                }
            } else {
                $message = '';
            }
            $message .= Mage::helper('mageworx_customercredit')->__('Returned credit amount: %s.', $payment->getOrder()->getBaseCurrency()->formatTxt($baseCreditAmountReturn));
            $historyRefund = $payment->getOrder()->getStatusHistoryCollection()->getLastItem();
            $historyRefund->setComment($message);
        }
        Mage::register('credit_need_refund',TRUE);
        return $this;
    }

    /**
     * Add credits to PayPal Cart
     * @param Varien_Event_Observer $observer
     * @return MageWorx_CustomerCredit_Model_Observer
     */
    public function paypalCart($observer) {
        $model = $observer->getEvent()->getPaypalCart();

        if (Mage::app()->getStore()->isAdmin()) {
            $allItems = Mage::getSingleton('adminhtml/sales_order_create')->getQuote()->getAllItems();
            $productIds = array();
            foreach ($allItems as $item) {
                $productIds[] = $item->getProductId();
            }
        } else {
            $productIds = Mage::getSingleton('checkout/cart')->getProductIds();            
        }

        if (count($productIds)==0) return $this;
        
        
        $address = $model->getSalesEntity()->getIsVirtual() ? $model->getSalesEntity()->getBillingAddress() : $model->getSalesEntity()->getShippingAddress();

        $credit = $address->getCustomerCreditAmount(); 
        if($credit == NULL)
        {
            $credit = 0;
            $credit = $model->getSalesEntity()->getCustomerCreditAmount();
        }
         $model->updateTotal(Mage_Paypal_Model_Cart::TOTAL_DISCOUNT,$credit);
    }
    
    # Paypal method for f... GoMage Checkout
    /**
    public function paypalCart($observer)
    {
        $model = $observer->getEvent()->getPaypalCart();

        if (Mage::app()->getStore()->isAdmin()) {
            $allItems = Mage::getSingleton('adminhtml/sales_order_create')->getQuote()->getAllItems();
            $productIds = array();
            foreach ($allItems as $item) {
                $productIds[] = $item->getProductId();
            }
        } else {
            $quoteId = $model->getSalesEntity()->getQuoteId();
            $quote = Mage::getSingleton('gomage_checkout/type_onestep')->getQuote();
            $quote = $quote->load($quoteId);
            foreach ($quote->getAllVisibleItems() as $item)
            {
                $productIds[] = $item->getProduct()->getId();
            }
        }

        if (count($productIds)==0) return $this;

        $address = $model->getSalesEntity()->getBillingAddress();
        foreach ($productIds as $productId) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $productTypeId = $product->getTypeId();
            if ($productTypeId!='downloadable' && !$product->isVirtual()) {
                $address = $model->getSalesEntity()->getShippingAddress();
                break;
            }
        }
        
        $credit = $address->getCustomerCreditAmount(); 
        if($credit == NULL)
        {
            $credit = 0;
            $credit = $model->getSalesEntity()->getCustomerCreditAmount();
        }
         $model->updateTotal(Mage_Paypal_Model_Cart::TOTAL_DISCOUNT,$credit);
    }
    */

    /**
     * Check creditmemo
     * @param Varien_Event_Observer $observer
     * @return MageWorx_CustomerCredit_Model_Observer
     */
    public function saveCreditmemoAfter(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getCreditmemo()->getOrder();
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        if(Mage::registry('credit_need_refund')) {
            Mage::getModel('mageworx_customercredit/credit', $customer)->processRefund($observer->getEvent()->getCreditmemo(), Mage::app()->getRequest()->getParam('creditmemo'));
        }
        return $this;
    }

    /**
     * Prepare credit rule to check
     * @param Varien_Event_Observer $observer
     * @return boolean
     */
    public function customercreditRule(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        if(Mage::registry('cc_order_refund')) {
            return true;
        }
        //if ($customerId = $order->getCustomerId()) {
            $store = $order->getStore();
            $customer = Mage::getModel('customer/customer')->setStore($store)->load($customerId);
            $customerGroupId = $customer->getGroupId();
            $handler = Mage::getSingleton('mageworx_customercredit/rules_handler');
            $handler->setCustomer($customer);
            $handler->setOrder($order);
            $websiteId = $store->getWebsiteId();
            $ruleModel = Mage::getResourceModel('mageworx_customercredit/rules_collection');
            $orderQty  = 0;//$order->getTotalQtyOrdered();
            $ruleModel->setValidationFilter($websiteId, $customerGroupId)->setRuleTypeFilter(MageWorx_CustomerCredit_Model_Rules::CC_RULE_TYPE_GIVE);
            foreach ($ruleModel->getData() as $rule) {
                $handler->execute($rule);
            }

        //}
    }

    /**
     * Return credit value to balance if order cancel
     * @param Varien_Event_Observer $observer
     * @return MageWorx_CustomerCredit_Model_Observer
     */
    public function returnCredit(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        Mage::getModel('mageworx_customercredit/credit', $customer)->processCancel($observer->getEvent()->getOrder());
        return $this;        
    }
    
    /**
     * Place order after
     * @param Varien_Event_Observer $observer
     * @return MageWorx_CustomerCredit_Model_Observer
     */
    public function placeOrderAfter(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $needUseCreditMarker = NULL;
        $needUseCreditMarker = Mage::registry('need_reduce_customercredit');
        $total_amount = $order->getCustomerCreditAmount();
        if($needUseCreditMarker) {
            $total_amount = $needUseCreditMarker;
        }
        Mage::register('customer_credit_order_place_amount_value',$total_amount,true);
        if ($this->reduceCustomerCreditValue($observer)) {
            // if payment of credit is fully -> invoice
            if ((Mage::helper('mageworx_customercredit')->isEnabledInvoiceOrder() && $order->getBaseTotalDue()==0 && $order->canInvoice()) ||
                ($order->canInvoice() && $needUseCreditMarker)    
                    ) {                
                $savedQtys = array();
                foreach ($order->getAllItems() as $orderItem) {
                    $savedQtys[$orderItem->getId()] = $orderItem->getQtyToInvoice();
                }
                $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($savedQtys);
                if (!$invoice->getTotalQty()) return $this;                

                if(!$needUseCreditMarker) {
                     $baseGrandTotal = $invoice->getBaseGrandTotal() - $invoice->getBaseCustomerCreditAmount();
                     $grandTotal = $invoice->getGrandTotal() - $invoice->getCustomerCreditAmount();
                     $invoice->setBaseGrandTotal($baseGrandTotal);
                     $invoice->setGrandTotal($grandTotal);
                 }
                $invoice->register();
                $invoice->getOrder()->setIsInProcess(true);
                
                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($invoice)
                    ->addObject($invoice->getOrder());
                try {
                $transactionSave->save();                
                } catch (Exception $e) {
                    
                }
            }
        }
        $this->placeOrderCustomer($order);
        Mage::getSingleton('customer/session')->unsCustomCreditValue();
        $session = Mage::getSingleton('checkout/session');
        $session->setUseInternalCredit(false);
        return $this;  
    }       
    
    /**
     * Check if status order compleate
     * @param Varien_Event_Observer $observer
     * @return MageWorx_CustomerCredit_Model_Observer
     */
    public function checkCompleteStatusOrder(Varien_Event_Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
       // if ($order->getStatus() == 'complete') {
            Mage::getModel('mageworx_customercredit/credit', $customer)->processCompleteOrderStatus($order);
            $this->customercreditRule($observer);
        //}    
        return $this;        
    }
    /**
     * Add customercredit link to head
     * @param Varien_Event_Observer $observer
     */
    public function toHtmlBlockBefore(Varien_Event_Observer $observer) {
        $block = $observer->getEvent()->getBlock();
        $blockName = $block->getNameInLayout();
        if ($blockName == 'customer_account_navigation') {
            if (Mage::helper('mageworx_customercredit')->isShowCustomerCredit()) $block->addLink('customercredit', 'customercredit', Mage::helper('mageworx_customercredit')->__('My Rewards points'),array("_secure"=>true));
        } 
    }
    
    /**
     * Check is can partitial payment
     * @return boolean
     */
    public function isPartialPayment() {
        return Mage::helper('mageworx_customercredit')->isPartialPayment(Mage::getSingleton('checkout/session')->getQuote(), Mage::getSingleton('customer/session')->getCustomerId(), Mage::app()->getStore()->getWebsiteId());
    }        

    /**
     * Add html blocks to layaut
     * @param Varien_Event_Observer $observer
     */
    public function toHtmlBlockAfter($observer) {
        $block = $observer->getEvent()->getBlock();
        $transport = $observer->getEvent()->getTransport();
        $helper = Mage::helper('mageworx_customercredit');
        if (!Mage::registry('customercredit_coupon_block') && $block instanceof Mage_Checkout_Block_Cart_Coupon && Mage::app()->getRequest()->getModuleName()!='firecheckout') {
            Mage::register('customercredit_coupon_block',true,true);
            $html = '';
            $partialPayment = $this->isPartialPayment();
            $creditValue = Mage::helper('mageworx_customercredit')->getCreditValue(Mage::getSingleton('customer/session')->getCustomer());
            if ($helper->isDisplayCreditBlockAtCart() && $partialPayment!=-3){
            $html .= '<div class="credit-payment box discount"><h4>'.Mage::helper('mageworx_customercredit')->__('Payment with Credit').'</h4>';
                if(Mage::getModel('checkout/session')->getUseInternalCredit()) {
                    $quote = Mage::getModel('checkout/cart')->getQuote();
                    if (!$quote->isVirtual()) {
                        $address = $quote->getShippingAddress();
                    } else {
                        $address = $quote->getBillingAddress();
                    }
                    $html .= $helper->__('You are using %s your credits to pay this order.',$helper->getValueExchangeRateMultiplied($address->getCustomerCreditAmount())).'<br/>';
                    $html .= '<a href="'.Mage::getUrl('customercredit/index/removeCreditUse').'">'.$helper->__("Don't use credit.").'</a>';
                    $customBlock = Mage::app()->getLayout()->createBlock("mageworx_customercredit/checkout_cartvalue", 'custom_value');
                    $customBlock->setTemplate('mageworx/customercredit/checkout/custom_value_cart.phtml');
                    $blockHtml = $customBlock->toHtml();
                    $blockHtml = str_replace("<div id='fakeCCDiv' style='display:none;'>","",$blockHtml);
                    $blockHtml = str_replace("</script></div>","</script>",$blockHtml);
                    $html .= $blockHtml;
                    
                } elseif ($partialPayment>0) {
                    $html .='<form action="'.Mage::getUrl('customercredit/index/updateCreditPost').'" method="post" id="credit-payment">
                        <p>'.$helper->__('Available credit amount: %s',round($creditValue,2)).' '.$helper->__('credits').' ('.Mage::helper('core')->currency($helper->getValueExchangeRateDivided($creditValue)).')</p>
                        <button type="submit" class="button"><span><span>'.$helper->__('Use Credit').'</span></span></button>
                    </form>';
                } else {
                    $html.='<p>'.$helper->__('Available credit amount: %s', $creditValue).' '.$helper->__('credits').' ('.Mage::helper('core')->currency($helper->getValueExchangeRateDivided($creditValue)).')</p>' . $helper->__('Your credit amount is not enough.').'</p>
                    <button type="button" class="button" onclick="setLocation(\''.Mage::getUrl('customercredit/').'\')"><span><span>'.$helper->__('Get Credit').'</span></span></button>';
                }
            $html .= '</div>';

            $html .= $transport->getHtml();
            $transport->setHtml($html);
            }
        }
        if($block instanceof Mage_Payment_Block_Info) {
            $html = $transport->getHtml();
            if($block->getInfo()->getQuote() && $block->getInfo()->getQuote()->getPayment()->getMethodInstance()->getCode()=="customercredit") return;
            $session           = Mage::getSingleton('checkout/session');
            $useInternalCredit = $session->getUseInternalCredit();
            if($useInternalCredit && (Mage::app()->getRequest()->getControllerName()!='order')) {
                $html .= " & ".Mage::helper("mageworx_customercredit")->__("Loyalty Booster");
            }
            $transport->setHtml($html);
        }
        if($block instanceof Mage_Catalog_Block_Product_Price) {
            $html = $transport->getHtml();
            $html = $this->addNotice($block,$html);
            $transport->setHtml($html);
        }
    }
    
    /**
     * Change customer group observer
     * @param Varien_Event_Observer $observer
     */
    
    public function changeGroup($observer) {
        $customer = $observer->getEvent()->getCustomer();
        $helper = Mage::helper('mageworx_customercredit');
        if($customer->hasDataChanges()) {
       //     $newValues = array_diff($customer->getData(), $customer->getOrigData());
            $websiteId = $customer->getWebsiteId();
            if($customer->getData('group_id')!=$customer->getOrigData('group_id')) {
                $customerGroup = $customer->getData('group_id');
                $time = $helper->getDefaultExpirationPeriod();
                if (Mage::getStoreConfig('mageworx_customercredit/expiration/default_expiration_period_'.$customerGroup)) {
                    $time = Mage::getStoreConfig('mageworx_customercredit/expiration/default_expiration_period_'.$customerGroup);
                }
                $credit = Mage::getModel('mageworx_customercredit/credit', $customer);
                if(!$time) {
                    $credit->setData('expiration_time',"0000-00-00");
                } else {
                    $credit->setData('expiration_time',date('Y-m-d',time()+3600*24*$time));
                }
                
                $credit->setIsCron(true)->save();
            }
        }
    }
    
    /**
     * Add Credits by Newsletter Subscription Action
     * @param Varien_Event_Observer $observer
     * @return MageWorx_CustomerCredit_Model_Observer
     */
    public function addCreditToCustomerForSubscription($observer) {
        $customer = $observer->getEvent()->getCustomer();
        if (Mage::app()->getRequest()->getParam('is_subscribed') == 1) {
            Mage::getModel('mageworx_customercredit/credit', $customer)->processSubscription();
        }
        return $this;
    }
    
    /**
     * Add Credits by Product Tag Action
     * @param Varien_Event_Observer $observer
     * @return MageWorx_CustomerCredit_Model_Observer
     */
    public function addCreditToCustomerForProductTag($observer) {
        $object = $observer->getObject();
        $customerId = $object->getData('first_customer_id');
        $customer = Mage::getModel('customer/customer')->load($customerId);
        Mage::getModel('mageworx_customercredit/credit', $customer)->processProductTag($observer->getObject());
        return $this;
    }
    
    /**
     * Add Credits by Product Review Action
     * @param Varien_Event_Observer $observer
     * @return MageWorx_CustomerCredit_Model_Observer
     */
    public function addCreditToCustomerForProductReview($observer) {
        $object = $observer->getObject();
        $customerId = $object->getCustomerId();
        $customer = Mage::getModel('customer/customer')->load($customerId);
        Mage::getModel('mageworx_customercredit/credit', $customer)->processProductReview($object);
        return $this;
    }
    
    /**
     * Add Customer Registration Rule
     * @param Varien_Event_Observer $observer
     * @return MageWorx_CustomerCredit_Model_Observer
     */
    public function customerRegisterSuccess($observer) {
        $handler = Mage::getSingleton('mageworx_customercredit/rules_handler');
        $customer = $observer->getEvent()->getCustomer();
        $handler->setCustomer($customer);

        $customerGroupId = $customer->getGroupId();
        
        $store = Mage::app()->getStore();
        $websiteId = $store->getWebsiteId();
        $ruleModel = Mage::getResourceModel('mageworx_customercredit/rules_collection');

        $ruleModel->setValidationFilter($websiteId, $customerGroupId)->setRuleTypeFilter(MageWorx_CustomerCredit_Model_Rules::CC_RULE_TYPE_GIVE);
        foreach ($ruleModel->getData() as $rule) {
            $handler->execute($rule);
        }
        return $this;
    }
    
    /**
     * Check Customer Birthday Cron
     * @return boolean
     */
    public function checkCustomerBirthdayCron() {
        $collection = Mage::getModel('customer/customer')->getCollection()->addAttributeToSelect('dob')->addAttributeToFilter('dob',array('like' => '%-'.date('m-d', time()).' %'));
        foreach($collection->getItems() as $customer)
        {
            try {
                Mage::getModel('mageworx_customercredit/credit', $customer)->processBirthday();
            } catch (Exception $e) {
                Mage::log($e->getMessage());
            }
        }
        return true;
    }
    
    /**
     * Add rules to observer
     * @param Mage_Sales_Model_Order $order
     * @return MageWorx_CustomerCredit_Model_Observer
     */
    public function placeOrderCustomer($order) {
        if(!$order->getQuote()) return; 
        $customer = $order->getQuote()->getCustomer();
        $handler = Mage::getSingleton('mageworx_customercredit/rules_handler');
        $handler->setCustomer($customer);
        $handler->setOrder($order);

        $customerGroupId = $customer->getGroupId();
        
        $store = Mage::app()->getStore($customer->getStoreId());
        $websiteId = $store->getWebsiteId();

        $ruleModel = Mage::getResourceModel('mageworx_customercredit/rules_collection');
        $ruleModel->setValidationFilter($websiteId, $customerGroupId)->setRuleTypeFilter(MageWorx_CustomerCredit_Model_Rules::CC_RULE_TYPE_GIVE);
        foreach ($ruleModel->getData() as $rule) {
            $handler->execute($rule);
        }
        return $this;
    }
    
    /**
     * Change expiration time if customer change group
     * @param type $observer
     */
    public function customerGroupSaveAfter($observer) {
        $days = Mage::app()->getRequest()->getParam('customercredit_expiration_in');
        Mage::getModel('core/config')->saveConfig('mageworx_customercredit/expiration/default_expiration_period_'.Mage::app()->getRequest()->getParam('id'),$days);
        $this->expirationDateRefreshCron();
    }
    
    /**
     * Change expiration time for customer group
     * @param type $observer
     */
    public function customerGroupLoadAfter($observer) {
        $groupModel = $observer->getEvent()->getObject();
        $days = Mage::getStoreConfig('mageworx_customercredit/expiration/default_expiration_period_'.$groupModel->getCustomerGroupId());
        $groupModel->setData('customercredit_expiration_in',$days);
        return $groupModel;
    }
    
    /**
     * Add html to block
     * @param type $observer
     * @return type
     */
    public function customerGroupPrepareLayoutAfter($observer) {
        $block = $observer->getBlock();
        if($block->getType()!='adminhtml/customer_group_edit_form') return ;
        $customerGroup = Mage::registry('current_group');

        $form = $block->getForm();
        $fieldset = $form->getElement('base_fieldset');
        $element = $fieldset->addField('customercredit_expiration_in', 'text',
            array(
                'name'  => 'customercredit_expiration_in',
                'label' => Mage::helper('mageworx_customercredit')->__('Loyalty Booster Expiration In'),
                'title' => Mage::helper('mageworx_customercredit')->__('Loyalty Booster Expiration In'),
                'note'  => Mage::helper('mageworx_customercredit')->__('day(s)'),
            )
        );
        $element->setValue($customerGroup->getData($element->getId()));

        $block->setForm($form);
    }

    /**
     * Check customercredit expiration time by cron
     * @return boolean
     */
    public function expirationDateCron() {
        $today = strtotime(date("Y-m-d"));
        $helper = Mage::helper('mageworx_customercredit');
        $model = Mage::getModel('mageworx_customercredit/credit');
        $collection = $model->getCollection()->joinCustomerTable();
        $sendCustomerNotificationPeriod = $helper->getNotifyExpirationDateLeft();
        foreach ($collection as $item) {
            if ($helper->isExpirationEnabled($item->getEnableExpiration())) {
                if($item->getExpirationTime()!='0000-00-00') {
                    $date = strtotime($item->getExpirationTime());
                    $hash = ($date-$today)/(3600*24);
                    if(($hash==$sendCustomerNotificationPeriod) && ($item->getValue()>0)) {
                        $customer = Mage::getModel('customer/customer')->load($item->getCustomerId());
                        $email = Mage::getModel('mageworx_customercredit/email');
                        $data = array('days_left' => $sendCustomerNotificationPeriod);
                        $customer->setData('customer_credit_data', $data);
                        $email->send(MageWorx_CustomerCredit_Model_Email::ACTION_EXPIRATION_NOTICE, $customer);
                    }
                    if($hash==0) {
                        $item->processExpire();
                    }
                }
            }
        }
    }
    
    /**
     * Add new task to cron when expiration time changed
     */
    public function expirationDateRefreshCron() {
        $model=Mage::getModel('mageworx_customercredit/credit');
        $collection = $model->getCollection()->joinCustomerTable();
        $helper = Mage::helper('mageworx_customercredit');
        $isRefreshAll = $helper->isEnabledUpdateExpirationDate();
        foreach ($collection as $item) {
            $customerGroup =$item->getGroupId();
            $time = $helper->getDefaultExpirationPeriod();
            if(Mage::getStoreConfig('mageworx_customercredit/expiration/default_expiration_period_'.$customerGroup)) {
                $time = Mage::getStoreConfig('mageworx_customercredit/expiration/default_expiration_period_'.$customerGroup);
            }
            
            if(($item->getExpirationTime()=='0000-00-00') || $isRefreshAll) {
                if(!$time) {
                    $item->setData('expiration_time',"0000-00-00");
                } else {
                    $item->setData('expiration_time',date('Y-m-d',time()+3600*24*$time));
                }
            }
        
            $item->setIsCron(true)->save();
        }
    }

    /**
     * @param $block
     * @param $html
     * @return string
     */
    public function addNotice($block,$html) {
        if(Mage::registry('notice_added')) return $html;
        if(!Mage::getSingleton('customer/session')->isLoggedIn()) return $html;
        if(!Mage::registry('current_product')) return $html;
        Mage::register('notice_added',true,true);
        $notice = NULL;
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customerId = $customer->getId()) {
            $store = Mage::app()->getStore();
            $product = Mage::registry('current_product');
            $customer = Mage::getModel('customer/customer')->setStore($store)->load($customerId);
            $customerGroupId = $customer->getGroupId();
            $websiteId = $store->getWebsiteId();
	    $ruleModel = Mage::getResourceModel('mageworx_customercredit/rules_collection');
            $ruleModel->setValidationFilter($websiteId, $customerGroupId)->setRuleTypeFilter(MageWorx_CustomerCredit_Model_Rules::CC_RULE_TYPE_APPLY);
	     foreach ($ruleModel->getData() as $rule) {           
                $conditions = unserialize($rule['conditions_serialized']);
                foreach ($conditions['conditions'] as $condition) {
                 // echo "<pre>";  print_r($conditions); exit;
                    $values = explode(',',$condition['value']);
                    if(($condition['attribute']=="sku") && (in_array($product->getSku(),$values))) {
                        $notice = '<div class="customercredit_notice">'.Mage::getStoreConfig('mageworx_customercredit/main/product_apply_notice').'</div>';
                        break;
                    }
                }
	    }
    	}
//        $notice = '<div class="customercredit_notice">'.Mage::helper('mageworx_customercredit')->__('This product can be paid using internal credits.').'</div>';
        return $html.$notice;
    }

    /**
     * @param $observer
     */
    public function savePayment($observer) {

        $data = $observer->getEvent()->getControllerAction()->getRequest()->getPost('payment', array());
        if (!empty($data['use_internal_credit']) || $data['method']=='customercredit') {
            Mage::getSingleton('checkout/session')->setUseInternalCredit(true);
        } else {
            Mage::getModel('checkout/session')->setUseInternalCredit(false);
        }
    }

    /**
     * @param $observer
     */
    public function savePaymentOneStep($observer) {

        $data['method'] = $observer->getPayment();

        if ($data['method']=='customercredit') { //
            Mage::getSingleton('checkout/session')->setUseInternalCredit(true);
        } else {
            Mage::getModel('checkout/session')->setUseInternalCredit(false);
        }
        
    }
    
    
    /**
     * @param $observer
     */
    public function isActivePaymentMethodFree($observer) {
        $code = $observer->getMethodInstance()->getCode();
        $method = $observer->getResult();
        if ($code == 'free' && Mage::getSingleton('checkout/session')->getUseInternalCredit()) {
            $method->isAvailable = false;
        }
    }
}